<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerSlotRequest;
use Illuminate\Http\Request;

class VolunteerSlotController extends Controller
{
    public function index()
    {
        $slotRequests = VolunteerSlotRequest::with(['volunteer', 'campaign'])
            ->orderByDesc('requested_date')
            ->orderByDesc('requested_start_time')
            ->paginate(20);

        return view('admin.volunteer-slots', compact('slotRequests'));
    }

    public function approve(Request $request, $id)
    {
        $slot = VolunteerSlotRequest::findOrFail($id);
        $slot->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Slot request approved.');
    }

    public function reject(Request $request, $id)
    {
        $slot = VolunteerSlotRequest::findOrFail($id);
        $slot->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Slot request rejected.');
    }
}
