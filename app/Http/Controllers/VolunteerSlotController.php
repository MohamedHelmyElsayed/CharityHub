<?php

namespace App\Http\Controllers;

use App\Models\VolunteerSlotRequest;
use App\Models\VolunteerHour;
use Illuminate\Http\Request;

class VolunteerSlotController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'nullable|exists:campaigns,id',
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_start_time' => 'required',
            'requested_end_time' => 'required|after:requested_start_time',
            'notes' => 'nullable|string|max:1000',
        ]);

        $volunteer = auth()->user()->volunteer;
        if (!$volunteer || $volunteer->status !== 'active') {
            return back()->with('error', 'You must be an active volunteer to book a slot.');
        }

        VolunteerSlotRequest::create([
            'volunteer_id' => $volunteer->id,
            'campaign_id' => $validated['campaign_id'] ?? null,
            'requested_date' => $validated['requested_date'],
            'requested_start_time' => $validated['requested_start_time'],
            'requested_end_time' => $validated['requested_end_time'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your slot request has been submitted and is pending admin approval.');
    }

    public function markComplete(Request $request, $id)
    {
        $slot = VolunteerSlotRequest::findOrFail($id);

        $volunteer = auth()->user()->volunteer;
        if (!$volunteer || $slot->volunteer_id !== $volunteer->id) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($slot->status !== 'approved') {
            return back()->with('error', 'This slot is not approved.');
        }

        if ($slot->completed_at) {
            return back()->with('error', 'This slot is already marked as completed.');
        }

        if (now()->format('Y-m-d') < $slot->requested_date->format('Y-m-d')) {
            return back()->with('error', 'You cannot mark this complete until the date has passed.');
        }

        $slot->update(['completed_at' => now()]);

        // Calculate hours
        $start = \Carbon\Carbon::parse($slot->requested_start_time);
        $end = \Carbon\Carbon::parse($slot->requested_end_time);
        $hours = $start->diffInMinutes($end) / 60;

        // Create pending hour log
        VolunteerHour::create([
            'volunteer_id' => $volunteer->id,
            'date' => $slot->requested_date,
            'hours' => round($hours, 2),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Slot marked as completed! Your hours are pending admin approval.');
    }
}
