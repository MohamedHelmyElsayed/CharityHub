<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VolunteerService;

class VolunteerController extends Controller
{
    protected $volunteerService;

    public function __construct(VolunteerService $volunteerService)
    {
        $this->volunteerService = $volunteerService;
    }

    public function index()
    {
        return view('pages.volunteer');
    }

    public function register(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to register as a volunteer.');
        }

        $this->volunteerService->registerAsVolunteer(auth()->user());

        return back()->with('success', 'Successfully registered as a volunteer!');
    }

    public function logHours(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5|max:24',
        ]);

        $volunteer = auth()->user()->volunteer;
        
        if (!$volunteer) {
            return back()->with('error', 'You must be a registered volunteer to log hours.');
        }

        $this->volunteerService->logHours($volunteer->id, $request->date, $request->hours);

        return back()->with('success', 'Hours logged successfully!');
    }
}
