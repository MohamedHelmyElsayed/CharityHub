<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerShift;
use App\Models\VolunteerEvent;
use Illuminate\Http\Request;

class VolunteerShiftController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'shift_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'required_volunteers' => 'required|integer|min:1',
        ]);

        VolunteerShift::create([
            'event_id' => $eventId,
            'title' => $request->title,
            'shift_date' => $request->shift_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'required_volunteers' => $request->required_volunteers,
            'status' => 'open',
        ]);

        return back()->with('success', 'Shift added successfully.');
    }

    public function destroy($id)
    {
        $shift = VolunteerShift::findOrFail($id);
        $shift->delete();

        return back()->with('success', 'Shift removed successfully.');
    }
}
