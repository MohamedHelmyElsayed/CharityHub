<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\FinancialLog;
use App\Models\HourLog;
use App\Models\AttendanceLog;
use App\Models\Volunteer;
use App\Models\VolunteerEvent;
use App\Models\VolunteerSchedule;
use App\Models\VolunteerSlotRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            // Financial
            'total_raised'         => Donation::where('status', 'completed')->sum('amount'),
            'total_donors'         => Donor::count(),
            'active_campaigns'     => Campaign::active()->count(),
            'total_campaigns'      => Campaign::count(),
            'total_donations'      => Donation::where('status', 'completed')->count(),
            'pending_donations'    => Donation::where('status', 'pending')->count(),
            // Volunteer
            'total_volunteers'     => Volunteer::count(),
            'pending_volunteers'   => Volunteer::pending()->count(),
            'approved_volunteers'  => Volunteer::approved()->count(),
            'total_volunteer_hours'=> \App\Models\VolunteerHour::sum('hours'),
            'total_approved_hours' => Volunteer::sum('total_approved_hours'),
            'pending_hour_logs'    => HourLog::where('status', 'pending_review')->count(),
            'pending_slot_requests'=> VolunteerSlotRequest::where('status', 'pending')->count(),
            'active_events'        => VolunteerEvent::where('status', 'open')->count(),
            'active_attendees'     => AttendanceLog::where('status', 'checked_in')->count(),
        ];

        $recentDonations = Donation::with(['donor', 'campaign'])
            ->orderByDesc('created_at')->limit(10)->get();

        $recentLogs = FinancialLog::with(['donor', 'campaign'])
            ->orderByDesc('created_at')->limit(10)->get();

        $pendingVolunteers = Volunteer::with('user')->pending()->latest()->limit(5)->get();
        $pendingHourLogs   = HourLog::with(['volunteer', 'attendanceLog.shift.event'])->where('status', 'pending_review')->latest()->limit(5)->get();
        $pendingRequests   = VolunteerSlotRequest::with(['volunteer', 'shift.event'])->where('status', 'pending')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'recentDonations', 'recentLogs',
            'pendingVolunteers', 'pendingHourLogs', 'pendingRequests'
        ));
    }

    public function ledger(Request $request)
    {
        $query = FinancialLog::with(['donor', 'campaign', 'donation']);

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $logs = $query->orderByDesc('created_at')->paginate(25);

        return view('admin.ledger', compact('logs'));
    }

    public function donors(Request $request)
    {
        $donors = Donor::with(['donations'])
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.donors', compact('donors'));
    }

    public function volunteerSchedules()
    {
        $schedules = VolunteerSchedule::with(['volunteers', 'campaign'])
            ->orderByDesc('event_date')
            ->paginate(20);

        $volunteers = Volunteer::where('status', 'active')->get();

        return view('admin.volunteer-schedules', compact('schedules', 'volunteers'));
    }

    public function assignVolunteer(Request $request, VolunteerSchedule $schedule)
    {
        $validated = $request->validate([
            'volunteer_id' => 'required|exists:volunteers,id',
        ]);

        $volunteer = Volunteer::findOrFail($validated['volunteer_id']);

        if ($volunteer->hasConflict($schedule)) {
            return back()->withErrors(['conflict' => 'Volunteer has a scheduling conflict for this time slot.']);
        }

        $volunteer->schedules()->syncWithoutDetaching([
            $schedule->id => ['status' => 'registered']
        ]);

        return back()->with('success', 'Volunteer assigned successfully.');
    }

    public function unassignVolunteer(VolunteerSchedule $schedule, Volunteer $volunteer)
    {
        $schedule->volunteers()->detach($volunteer->id);

        return back()->with('success', 'Volunteer unassigned successfully.');
    }
}
