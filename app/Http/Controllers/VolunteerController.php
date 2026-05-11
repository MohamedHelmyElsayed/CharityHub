<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\HourLog;
use App\Models\VolunteerEvent;
use App\Models\VolunteerSlotRequest;
use App\Models\VolunteerShift;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function index()
    {
        // New event-based data
        $events = VolunteerEvent::with('shifts')->upcoming()->latest('start_date')->take(9)->get();

        // Legacy schedule-based data (still used by volunteer.blade.php form dropdown)
        $schedules = \App\Models\VolunteerSchedule::with('campaign')
            ->where('status', 'scheduled')
            ->where('event_date', '>=', today())
            ->orderBy('event_date')
            ->get();

        $campaigns = \App\Models\Campaign::active()
            ->whereHas('volunteerSchedules', function ($q) {
                $q->where('status', 'scheduled')->where('event_date', '>=', today());
            })->get();

        $myVolunteer = auth()->check() ? auth()->user()->volunteer : null;

        return view('pages.volunteer', compact('events', 'schedules', 'campaigns', 'myVolunteer'));
    }

    public function profile()
    {
        $myVolunteer = auth()->user()->volunteer;
        if (!$myVolunteer) {
            return redirect()->route('volunteer.index')->with('info', 'Please register as a volunteer first.');
        }
        return view('pages.volunteer-profile-edit', compact('myVolunteer'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'nullable|string|max:20',
            'skills'   => 'nullable|string|max:500',
            'bio'      => 'nullable|string|max:1000',
        ]);

        $skillsArray = $request->filled('skills')
            ? array_map('trim', explode(',', $validated['skills']))
            : [];

        Volunteer::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'name'   => $validated['name'],
                'email'  => $validated['email'],
                'phone'  => $validated['phone'] ?? null,
                'skills' => $skillsArray,
                'bio'    => $validated['bio'] ?? null,
                'status' => 'pending',
            ]
        );

        return redirect()->route('volunteer.dashboard')->with('success', 'Volunteer application submitted! An admin will review it shortly.');
    }

    public function dashboard()
    {
        $user      = auth()->user();
        $volunteer = $user->volunteer;

        if (!$volunteer) {
            return redirect()->route('volunteer.index')->with('info', 'Please register as a volunteer first.');
        }

        // ── Stats ──────────────────────────────────────────────────────────────
        $totalApprovedHours = $volunteer->total_approved_hours;
        $pendingHours       = HourLog::where('volunteer_id', $volunteer->id)->where('status', 'pending_review')->sum('calculated_hours');
        $completedShifts    = AttendanceLog::where('volunteer_id', $volunteer->id)->whereIn('status', ['checked_out', 'verified'])->count();

        // ── Upcoming Shifts (safe join-based ordering) ─────────────────────────
        $upcomingRequests = VolunteerSlotRequest::with(['shift.event'])
            ->where('volunteer_slot_requests.volunteer_id', $volunteer->id)
            ->where('volunteer_slot_requests.status', 'approved')
            ->whereNotNull('volunteer_slot_requests.shift_id')
            ->join('volunteer_shifts', 'volunteer_slot_requests.shift_id', '=', 'volunteer_shifts.id')
            ->where('volunteer_shifts.shift_date', '>=', today())
            ->orderBy('volunteer_shifts.shift_date')
            ->orderBy('volunteer_shifts.start_time')
            ->select('volunteer_slot_requests.*')
            ->limit(5)->get();

        // ── Available Events ───────────────────────────────────────────────────
        $availableEvents = VolunteerEvent::with('shifts')->upcoming()->take(6)->get();

        // ── Slot Requests History (all, including legacy without shift_id) ──────
        $slotRequests = VolunteerSlotRequest::with(['shift.event'])
            ->where('volunteer_id', $volunteer->id)
            ->latest()->limit(10)->get();

        // ── Active Check-Ins (Checked in but not out) ────────────────────────
        $activeCheckIns = AttendanceLog::where('volunteer_id', $volunteer->id)
            ->whereNull('check_out')
            ->where('status', 'checked_in')
            ->get()->keyBy('shift_id');

        // ── Attendance History ─────────────────────────────────────────────────
        $attendanceHistory = AttendanceLog::with(['shift.event'])
            ->where('volunteer_id', $volunteer->id)
            ->latest('check_in')->limit(10)->get();

        // ── Hour Logs ──────────────────────────────────────────────────────────
        $hourLogs = HourLog::with('attendanceLog.shift.event')
            ->where('volunteer_id', $volunteer->id)
            ->latest()->limit(10)->get();

        // ── Legacy compatibility: old schedules ────────────────────────────────
        $upcomingSchedules = $volunteer->schedules()
            ->where('event_date', '>=', today())
            ->where('volunteer_schedules.status', 'scheduled')
            ->orderBy('event_date')->limit(5)->get();

        $pastSchedules = $volunteer->schedules()
            ->where('event_date', '<', today())
            ->orderByDesc('event_date')->limit(5)->get();

        // ── Donations (if also a donor) ────────────────────────────────────────
        $donationStats = [
            'total_donated'      => \App\Models\Donation::where('user_id', $user->id)->completed()->sum('amount'),
            'donation_count'     => \App\Models\Donation::where('user_id', $user->id)->completed()->count(),
            'certificates_count' => \App\Models\Donation::where('user_id', $user->id)->whereNotNull('certificate_uuid')->count(),
        ];
        $recentDonations = \App\Models\Donation::with('campaign')
            ->where('user_id', $user->id)->latest()->limit(5)->get();

        $campaigns = \App\Models\Campaign::active()->get();

        return view('pages.volunteer-dashboard', compact(
            'volunteer', 'totalApprovedHours', 'pendingHours', 'completedShifts',
            'upcomingRequests', 'availableEvents', 'slotRequests',
            'activeCheckIns', 'attendanceHistory', 'hourLogs',
            'upcomingSchedules', 'pastSchedules',
            'donationStats', 'recentDonations', 'campaigns'
        ));
    }

    public function logHours(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:volunteer_schedule_user,volunteer_schedule_id',
            'hours'       => 'required|numeric|min:0.5|max:24',
        ]);

        $volunteer = auth()->user()->volunteer;
        if (!$volunteer) {
            return response()->json(['error' => 'Not a registered volunteer.'], 403);
        }

        $volunteer->schedules()->updateExistingPivot($validated['schedule_id'], [
            'hours_worked' => $validated['hours'],
            'status'       => 'attended',
        ]);

        \App\Models\VolunteerHour::create([
            'volunteer_id'           => $volunteer->id,
            'volunteer_schedule_id'  => $validated['schedule_id'],
            'date'                   => now()->toDateString(),
            'hours'                  => $validated['hours'],
            'status'                 => 'pending',
        ]);

        return back()->with('success', 'Hours logged and pending admin approval.');
    }

    public function showSchedule(\App\Models\VolunteerSchedule $schedule)
    {
        return view('pages.volunteer-schedule-details', compact('schedule'));
    }
}
