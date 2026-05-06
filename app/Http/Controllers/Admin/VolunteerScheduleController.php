<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerSchedule;
use App\Models\Campaign;
use Illuminate\Http\Request;

class VolunteerScheduleController extends Controller
{
    public function index()
    {
        $schedules = VolunteerSchedule::with(['campaign', 'volunteers'])
            ->orderByDesc('event_date')
            ->paginate(15);
            
        $volunteers = \App\Models\Volunteer::where('status', 'active')->get();
            
        return view('admin.volunteer-schedules', compact('schedules', 'volunteers'));
    }

    public function create()
    {
        $campaigns = Campaign::active()->get();
        return view('admin.volunteer-schedule-form', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'event_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_volunteers' => 'required|integer|min:1',
        ]);

        VolunteerSchedule::create($validated);

        return redirect()->route('custom_admin.schedules.index')->with('success', 'Schedule created successfully.');
    }

    public function edit($id)
    {
        $schedule = VolunteerSchedule::findOrFail($id);
        $campaigns = Campaign::active()->get();
        return view('admin.volunteer-schedule-form', compact('schedule', 'campaigns'));
    }

    public function update(Request $request, $id)
    {
        $schedule = VolunteerSchedule::findOrFail($id);
        
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'event_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_volunteers' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,cancelled,completed',
        ]);

        $schedule->update($validated);

        return redirect()->route('custom_admin.schedules.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id)
    {
        VolunteerSchedule::findOrFail($id)->delete();
        return redirect()->route('custom_admin.schedules.index')->with('success', 'Schedule deleted successfully.');
    }
}
