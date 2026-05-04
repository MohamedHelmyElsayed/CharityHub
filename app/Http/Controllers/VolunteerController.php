<?php

namespace App\Http\Controllers;

use App\Models\VolunteerSchedule;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function index()
    {
        $schedules = VolunteerSchedule::where('status', 'scheduled')
            ->where('event_date', '>=', today())
            ->orderBy('event_date')
            ->get();

        $myVolunteer = auth()->check()
            ? auth()->user()->volunteer
            : null;

        return view('pages.volunteer', compact('schedules', 'myVolunteer'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'bio' => 'nullable|string|max:1000',
            'schedule_id' => 'nullable|exists:volunteer_schedules,id',
        ]);

        // Find or create volunteer profile
        $volunteer = Volunteer::firstOrCreate(
            ['email' => $validated['email']],
            [
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'skills' => $validated['skills'] ?? [],
                'bio' => $validated['bio'] ?? null,
                'status' => 'active',
            ]
        );

        $response = ['message' => 'Volunteer profile created/updated.'];

        // Register for a specific schedule if requested
        if (!empty($validated['schedule_id'])) {
            $schedule = VolunteerSchedule::findOrFail($validated['schedule_id']);

            // Conflict detection
            if ($volunteer->hasConflict($schedule)) {
                return response()->json([
                    'error' => 'Scheduling conflict: You are already registered for another event during this time.',
                    'conflict' => true,
                ], 409);
            }

            if ($schedule->isAtCapacity()) {
                return response()->json(['error' => 'This event is at full capacity.'], 409);
            }

            // Register
            $volunteer->schedules()->syncWithoutDetaching([
                $schedule->id => ['status' => 'registered']
            ]);

            $response['message'] = 'Successfully registered for ' . $schedule->event_name;
        }

        return response()->json($response);
    }

    public function dashboard()
    {
        $volunteer = auth()->user()->volunteer;
        if (!$volunteer) {
            return redirect()->route('volunteer.index')->with('info', 'Please register as a volunteer first.');
        }

        $upcomingSchedules = $volunteer->schedules()
            ->where('event_date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('event_date')
            ->get();

        $pastSchedules = $volunteer->schedules()
            ->where('event_date', '<', today())
            ->orderByDesc('event_date')
            ->get();

        return view('pages.volunteer-dashboard', compact('volunteer', 'upcomingSchedules', 'pastSchedules'));
    }

    public function logHours(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:volunteer_schedule_user,volunteer_schedule_id',
            'hours' => 'required|numeric|min:0.5|max:24',
        ]);

        $volunteer = auth()->user()->volunteer;
        if (!$volunteer) {
            return response()->json(['error' => 'Not a registered volunteer.'], 403);
        }

        $volunteer->schedules()->updateExistingPivot($validated['schedule_id'], [
            'hours_worked' => $validated['hours'],
            'status' => 'attended',
        ]);

        return response()->json(['message' => 'Hours logged successfully.']);
    }
}
