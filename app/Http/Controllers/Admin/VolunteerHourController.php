<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerHour;
use Illuminate\Http\Request;

class VolunteerHourController extends Controller
{
    public function index()
    {
        $hourLogs = VolunteerHour::with(['volunteer', 'schedule'])
            ->orderByDesc('created_at')
            ->paginate(20);
            
        return view('admin.volunteer-hours', compact('hourLogs'));
    }

    public function approve(VolunteerHour $log)
    {
        $log->update(['status' => 'approved']);
        
        // Update volunteer total hours
        $volunteer = $log->volunteer;
        $volunteer->increment('total_hours', $log->hours);

        return back()->with('success', 'Hours approved successfully.');
    }
}
