<?php

namespace App\Http\Controllers\Volunteer;

use App\Events\VolunteerCheckedIn;
use App\Events\VolunteerCheckedOut;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\VolunteerShift;
use App\Services\HourCalculationService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private HourCalculationService $hourService) {}

    /**
     * Admin checks a volunteer in manually.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'volunteer_id' => 'required|exists:volunteers,id',
            'shift_id'     => 'required|exists:volunteer_shifts,id',
        ]);

        // Prevent duplicate check-in
        $existing = AttendanceLog::where('volunteer_id', $request->volunteer_id)
            ->where('shift_id', $request->shift_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Volunteer is already checked in for this shift.');
        }

        // Ensure volunteer has an approved slot request
        $hasRequest = \App\Models\VolunteerSlotRequest::where('volunteer_id', $request->volunteer_id)
            ->where('shift_id', $request->shift_id)
            ->where('status', 'approved')
            ->exists();

        if (!$hasRequest) {
            return back()->with('error', 'Volunteer does not have an approved slot request for this shift.');
        }

        $log = AttendanceLog::create([
            'volunteer_id'    => $request->volunteer_id,
            'shift_id'        => $request->shift_id,
            'check_in'        => now(),
            'check_in_method' => 'manual',
            'ip_address'      => $request->ip(),
            'status'          => 'checked_in',
        ]);

        event(new VolunteerCheckedIn($log));

        return back()->with('success', 'Volunteer checked in successfully.');
    }

    /**
     * Admin checks a volunteer out manually.
     */
    public function checkOut(Request $request, AttendanceLog $log)
    {
        if ($log->check_out) {
            return back()->with('error', 'Volunteer has already been checked out.');
        }

        $log->update([
            'check_out'        => now(),
            'check_out_method' => 'manual',
            'status'           => 'checked_out',
        ]);

        event(new VolunteerCheckedOut($log));

        return back()->with('success', 'Volunteer checked out. Hours are now pending review.');
    }

    /**
     * Volunteer self check-in via QR code token.
     */
    public function qrCheckIn(Request $request, string $token)
    {
        $shift = VolunteerShift::where('qr_token', $token)->first();

        if (!$shift || !$shift->isQrTokenValid($token)) {
            return response()->json(['error' => 'Invalid or expired QR code.'], 422);
        }

        $volunteer = auth()->user()?->volunteer;
        if (!$volunteer) {
            return response()->json(['error' => 'Volunteer profile not found.'], 403);
        }

        $existing = AttendanceLog::where('volunteer_id', $volunteer->id)
            ->where('shift_id', $shift->id)->first();

        if ($existing) {
            return response()->json(['error' => 'You are already checked in.'], 422);
        }

        $log = AttendanceLog::create([
            'volunteer_id'    => $volunteer->id,
            'shift_id'        => $shift->id,
            'check_in'        => now(),
            'check_in_method' => 'qr_code',
            'ip_address'      => $request->ip(),
            'status'          => 'checked_in',
        ]);

        event(new VolunteerCheckedIn($log));

        return response()->json(['success' => 'Checked in successfully!', 'log_id' => $log->id]);
    }
}
