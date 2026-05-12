<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\HourLog;
use App\Models\VolunteerApplication;
use App\Models\VolunteerEvent;
use App\Models\VolunteerSlotRequest;
use App\Models\VolunteerShift;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    // ── Public: Opportunities Listing ─────────────────────────────────────────

    public function opportunities()
    {
        $opportunities = VolunteerEvent::with(['shifts', 'campaign'])
            ->withCount('approvedApplications')
            ->whereIn('status', ['open', 'full', 'completed'])
            ->orderByDesc('start_date')
            ->get();

        $categories = $opportunities->pluck('category')
            ->merge($opportunities->pluck('event_type'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('pages.volunteering-opportunities', compact('opportunities', 'categories'));
    }

    // ── Public: Opportunity Detail ─────────────────────────────────────────────

    public function showOpportunity(VolunteerEvent $event)
    {
        $event->load(['shifts', 'campaign', 'applications']);
        $opportunity = $event;

        // Current user's application for this opportunity
        $userApplication = null;
        $mySlotRequestIds = collect();

        if (auth()->check()) {
            $userApplication = VolunteerApplication::where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->first();

            // Shifts this user has already requested
            $volunteer = auth()->user()->volunteer;
            if ($volunteer) {
                $mySlotRequestIds = VolunteerSlotRequest::where('volunteer_id', $volunteer->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->pluck('shift_id');
            }
        }

        return view('pages.volunteering-opportunity-detail',
            compact('opportunity', 'userApplication', 'mySlotRequestIds'));
    }

    // ── Legacy: Volunteer Index (redirects to new opportunities page) ──────────

    public function index()
    {
        return redirect()->route('volunteering.index');
    }

    // ── Volunteer Profile & Registration ──────────────────────────────────────

    public function profile()
    {
        $myVolunteer = auth()->user()->volunteer;
        if (!$myVolunteer) {
            return redirect()->route('volunteering.index')
                ->with('info', 'Please apply for an opportunity first.');
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

        return redirect()->route('volunteer.dashboard')
            ->with('success', 'Profile updated!');
    }

    // ── Volunteer Dashboard ────────────────────────────────────────────────────

    public function dashboard()
    {
        $user      = auth()->user();
        $volunteer = $user->volunteer;

        if (!$volunteer) {
            return redirect()->route('volunteering.index')
                ->with('info', 'Browse and apply for an opportunity to get started.');
        }

        // ── Stats ──────────────────────────────────────────────────────────────
        $totalApprovedHours = $volunteer->total_approved_hours;
        $pendingHours       = HourLog::where('volunteer_id', $volunteer->id)
            ->where('status', 'pending_review')->sum('calculated_hours');
        $completedShifts    = AttendanceLog::where('volunteer_id', $volunteer->id)
            ->whereIn('status', ['checked_out', 'verified'])->count();

        // ── My Applications (per opportunity) ──────────────────────────────────
        $myApplications = VolunteerApplication::with('event')
            ->where('user_id', $user->id)
            ->latest()->get();

        $approvedApplications = $myApplications->where('status', 'approved');
        $pendingApplications  = $myApplications->where('status', 'pending');

        // ── Upcoming Approved Shifts (scoped to approved opportunities) ─────────
        $approvedEventIds = $approvedApplications->pluck('event_id');

        $upcomingRequests = VolunteerSlotRequest::with(['shift.event'])
            ->where('volunteer_slot_requests.volunteer_id', $volunteer->id)
            ->where('volunteer_slot_requests.status', 'approved')
            ->whereNotNull('volunteer_slot_requests.shift_id')
            ->join('volunteer_shifts', 'volunteer_slot_requests.shift_id', '=', 'volunteer_shifts.id')
            ->whereIn('volunteer_shifts.event_id', $approvedEventIds->toArray() ?: [0])
            ->where('volunteer_shifts.shift_date', '>=', today())
            ->orderBy('volunteer_shifts.shift_date')
            ->orderBy('volunteer_shifts.start_time')
            ->select('volunteer_slot_requests.*')
            ->limit(5)->get();

        // ── Active Check-Ins ───────────────────────────────────────────────────
        $activeCheckIns = AttendanceLog::where('volunteer_id', $volunteer->id)
            ->whereNull('check_out')
            ->where('status', 'checked_in')
            ->get()->keyBy('shift_id');

        // ── Completed Shift IDs ────────────────────────────────────────────────
        $completedShiftIds = AttendanceLog::where('volunteer_id', $volunteer->id)
            ->where('status', 'verified')
            ->whereHas('hourLog', fn ($q) => $q->whereIn('status', ['approved', 'adjusted']))
            ->pluck('shift_id')->toArray();

        // ── Slot Requests history ──────────────────────────────────────────────
        $slotRequests = VolunteerSlotRequest::with(['shift.event'])
            ->where('volunteer_id', $volunteer->id)
            ->latest()->limit(10)->get();

        // ── Attendance History ─────────────────────────────────────────────────
        $attendanceHistory = AttendanceLog::with(['shift.event'])
            ->where('volunteer_id', $volunteer->id)
            ->latest('check_in')->limit(10)->get();

        // ── Hour Logs ──────────────────────────────────────────────────────────
        $hourLogs = HourLog::with('attendanceLog.shift.event')
            ->where('volunteer_id', $volunteer->id)
            ->latest()->limit(10)->get();

        // ── Legacy schedules (backward compat) ─────────────────────────────────
        $upcomingSchedules = $volunteer->schedules()
            ->where('event_date', '>=', today())
            ->where('volunteer_schedules.status', 'scheduled')
            ->orderBy('event_date')->limit(5)->get();

        $pastSchedules = $volunteer->schedules()
            ->where('event_date', '<', today())
            ->orderByDesc('event_date')->limit(5)->get();

        // ── Available Opportunities (for approved volunteers) ───────────────────
        $availableEvents = VolunteerEvent::with('shifts')
            ->whereIn('status', ['open'])
            ->whereNotIn('id', $myApplications->pluck('event_id')->toArray())
            ->take(4)->get();

        // ── Donations ──────────────────────────────────────────────────────────
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
            'myApplications', 'approvedApplications', 'pendingApplications',
            'upcomingRequests', 'availableEvents', 'slotRequests',
            'activeCheckIns', 'attendanceHistory', 'hourLogs',
            'completedShiftIds',
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
            'volunteer_id'          => $volunteer->id,
            'volunteer_schedule_id' => $validated['schedule_id'],
            'date'                  => now()->toDateString(),
            'hours'                 => $validated['hours'],
            'status'                => 'pending',
        ]);

        return back()->with('success', 'Hours logged and pending admin approval.');
    }

    public function showSchedule(\App\Models\VolunteerSchedule $schedule)
    {
        return view('pages.volunteer-schedule-details', compact('schedule'));
    }
}
