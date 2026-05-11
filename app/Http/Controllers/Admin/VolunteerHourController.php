<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerHour;
use Illuminate\Http\Request;

class VolunteerHourController extends Controller
{
    public function index()
    {
        // Use the new Enterprise HourLog model
        $hourLogs = \App\Models\HourLog::with(['volunteer', 'attendanceLog.shift.event'])
            ->orderByRaw("FIELD(status, 'pending_review') DESC")
            ->orderByDesc('created_at')
            ->paginate(20);
            
        return view('admin.volunteer-hours', compact('hourLogs'));
    }

    public function approve(Request $request, \App\Models\HourLog $log, \App\Services\HourCalculationService $hourService)
    {
        // Admin can optionally adjust the hours before approving
        $adjustedHours = $request->input('adjusted_hours');
        $reason = $request->input('adjustment_reason');

        // Approve using the service which handles volunteer totals, attendance status, and events
        $hourService->approve($log, auth()->user(), $adjustedHours, $reason);

        return back()->with('success', 'Hour log processed successfully.');
    }
}
