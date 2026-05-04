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
        $volunteers = $this->volunteerService->getAllVolunteers();
        return view('admin.volunteers', compact('volunteers'));
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
            'status' => 'required|in:active,inactive',
        ]);

        $volunteer = \App\Models\Volunteer::findOrFail($id);
        $volunteer->update(['status' => $request->status]);

        return back()->with('success', 'Volunteer status updated.');
    }
}
