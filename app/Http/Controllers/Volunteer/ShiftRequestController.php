<?php

namespace App\Http\Controllers\Volunteer;

use App\Events\ShiftApproved;
use App\Events\ShiftRejected;
use App\Http\Controllers\Controller;
use App\Models\VolunteerShift;
use App\Models\VolunteerSlotRequest;
use App\Services\ShiftConflictService;
use Illuminate\Http\Request;

class ShiftRequestController extends Controller
{
    public function __construct(private ShiftConflictService $conflictService) {}

    /**
     * Volunteer requests to join a shift.
     * Only allowed if the volunteer is approved for the opportunity that owns this shift.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:volunteer_shifts,id',
            'notes'    => 'nullable|string|max:500',
        ]);

        $volunteer = auth()->user()?->volunteer;
        if (!$volunteer) {
            return back()->with('error', 'You must have a volunteer profile to request shifts.');
        }

        $shift = VolunteerShift::with('event')->findOrFail($request->shift_id);

        // Auto-create application if missing
        $application = \App\Models\VolunteerApplication::where('user_id', auth()->id())
            ->where('event_id', $shift->event_id)
            ->first();

        if (!$application) {
            $application = \App\Models\VolunteerApplication::create([
                'user_id'        => auth()->id(),
                'event_id'       => $shift->event_id,
                'status'         => 'pending',
                'motivation'     => 'Auto-applied via shift request.',
                'skills_offered' => implode(', ', $volunteer->skills ?? []),
                'availability'   => 'As per shift request',
            ]);
        }

        // Single validate() call handles: full, deadline, duplicate, opportunity scope, conflict
        $error = $this->conflictService->validate($volunteer, $shift);
        if ($error) {
            return back()->with('error', $error);
        }

        VolunteerSlotRequest::create([
            'volunteer_id'         => $volunteer->id,
            'shift_id'             => $shift->id,
            'campaign_id'          => $shift->event?->campaign_id,
            'notes'                => $request->notes,
            'status'               => 'pending',
            'requested_at'         => now(),
            'requested_date'       => $shift->shift_date,
            'requested_start_time' => $shift->start_time,
            'requested_end_time'   => $shift->end_time,
        ]);

        return back()->with('success', 'Shift request submitted! You will be notified once it is reviewed.');
    }

    /**
     * Volunteer cancels a pending request.
     */
    public function cancel(VolunteerSlotRequest $slotRequest)
    {
        $volunteer = auth()->user()?->volunteer;

        if (!$volunteer || $slotRequest->volunteer_id !== $volunteer->id) {
            abort(403);
        }

        if ($slotRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $slotRequest->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Shift request cancelled.');
    }

    // ── Admin Actions ──────────────────────────────────────────────────────────

    public function approve(Request $request, VolunteerSlotRequest $slotRequest)
    {
        if ($slotRequest->status !== 'pending') {
            return back()->with('error', 'Request is not in pending status.');
        }

        $slotRequest->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $slotRequest->shift?->incrementAssignedCount();
        event(new ShiftApproved($slotRequest));

        return back()->with('success', 'Shift request approved and volunteer notified.');
    }

    public function reject(Request $request, VolunteerSlotRequest $slotRequest)
    {
        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $slotRequest->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejected_by'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        event(new ShiftRejected($slotRequest));

        return back()->with('success', 'Shift request rejected and volunteer notified.');
    }
}
