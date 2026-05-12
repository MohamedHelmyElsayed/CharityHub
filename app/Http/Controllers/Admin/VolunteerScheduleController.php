<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerEvent;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VolunteerScheduleController extends Controller
{
    public function index()
    {
        $schedules = VolunteerEvent::with(['campaign', 'shifts'])
            ->withCount('approvedApplications')
            ->orderByDesc('start_date')
            ->paginate(15);
            
        return view('admin.volunteer-schedules', compact('schedules'));
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'max_volunteers' => 'required|integer|min:1',
            'event_type' => 'required|string',
            'category' => 'required|string',
            'required_skills' => 'nullable|string',
            'registration_deadline' => 'nullable|date|before_or_equal:start_date',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . rand(100, 999);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';
        
        // Convert skills string to array if provided
        if ($request->filled('required_skills')) {
            $validated['required_skills'] = array_map('trim', explode(',', $validated['required_skills']));
        }

        VolunteerEvent::create($validated);

        return redirect()->route('custom_admin.schedules.index')->with('success', 'Volunteer Opportunity created successfully.');
    }

    public function edit($id)
    {
        $schedule = VolunteerEvent::findOrFail($id);
        $campaigns = Campaign::active()->get();
        return view('admin.volunteer-schedule-form', compact('schedule', 'campaigns'));
    }

    public function update(Request $request, $id)
    {
        $event = VolunteerEvent::findOrFail($id);
        
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'max_volunteers' => 'required|integer|min:1',
            'event_type' => 'required|string',
            'category' => 'required|string',
            'required_skills' => 'nullable|string',
            'status' => 'required|in:open,full,completed,cancelled',
            'registration_deadline' => 'nullable|date|before_or_equal:start_date',
        ]);

        if ($request->filled('required_skills')) {
            $validated['required_skills'] = array_map('trim', explode(',', $validated['required_skills']));
        } else {
            $validated['required_skills'] = [];
        }

        $event->update($validated);

        return redirect()->route('custom_admin.schedules.index')->with('success', 'Opportunity updated successfully.');
    }

    public function destroy($id)
    {
        VolunteerEvent::findOrFail($id)->delete();
        return redirect()->route('custom_admin.schedules.index')->with('success', 'Opportunity deleted successfully.');
    }
}
