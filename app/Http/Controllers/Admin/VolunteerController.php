<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VolunteerService;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    protected $volunteerService;

    public function __construct(VolunteerService $volunteerService)
    {
        $this->volunteerService = $volunteerService;
    }

    public function index()
    {
        $pendingApplications = \App\Models\VolunteerApplication::with(['user', 'event'])->where('status', 'pending')->latest()->get();
        $activeVolunteers = \App\Models\Volunteer::with('user')->where('status', 'active')->get();
        return view('admin.volunteers', compact('pendingApplications', 'activeVolunteers'));
    }

    public function show($id)
    {
        $stats = $this->volunteerService->getVolunteerStats($id);
        $volunteer = \App\Models\Volunteer::with('user')->findOrFail($id);
        return view('admin.volunteers', compact('volunteer', 'stats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending,rejected,approved',
        ]);

        // Handle VolunteerApplication status update
        if ($request->has('application_id')) {
            $application = \App\Models\VolunteerApplication::findOrFail($request->application_id);
            if ($request->status === 'approved') {
                $application->approve(auth()->id());
                
                // Create or update Volunteer profile
                \App\Models\Volunteer::updateOrCreate(
                    ['user_id' => $application->user_id],
                    [
                        'name'   => $application->user->name,
                        'email'  => $application->user->email,
                        'status' => 'active',
                        'skills' => explode(',', $application->skills_offered),
                        'bio'    => $application->motivation,
                    ]
                );
                return back()->with('success', 'Application approved and volunteer profile created.');
            } elseif ($request->status === 'rejected') {
                $application->reject(auth()->id());
                return back()->with('success', 'Application rejected.');
            }
        }

        // Handle legacy/existing Volunteer status update
        $volunteer = \App\Models\Volunteer::findOrFail($id);
        $volunteer->update(['status' => $request->status]);

        return back()->with('success', 'Volunteer status updated.');
    }
}
