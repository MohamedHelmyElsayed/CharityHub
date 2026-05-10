<?php

namespace App\Services;

use App\Events\HoursApproved;
use App\Models\AttendanceLog;
use App\Models\HourLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HourCalculationService
{
    /**
     * Calculate and record hours from an attendance log after check-out.
     */
    public function calculateFromAttendance(AttendanceLog $log): HourLog
    {
        $hours = $this->calculate($log);

        $hourLog = HourLog::updateOrCreate(
            ['attendance_log_id' => $log->id],
            [
                'volunteer_id'     => $log->volunteer_id,
                'calculated_hours' => $hours,
                'status'           => 'pending_review',
            ]
        );

        return $hourLog;
    }

    /**
     * Calculate hours between check_in and check_out.
     */
    public function calculate(AttendanceLog $log): float
    {
        if (!$log->check_out) return 0.0;
        return round($log->check_in->floatDiffInHours($log->check_out), 2);
    }

    /**
     * Approve an hour log and add hours to volunteer's total.
     */
    public function approve(HourLog $log, User $admin, ?float $adjustedHours = null, ?string $reason = null): void
    {
        DB::transaction(function () use ($log, $admin, $adjustedHours, $reason) {
            $finalHours = $adjustedHours ?? $log->calculated_hours;
            $status     = $adjustedHours ? 'adjusted' : 'approved';

            $log->update([
                'approved_hours'     => $finalHours,
                'adjustment_reason'  => $reason,
                'approved_by'        => $admin->id,
                'approved_at'        => now(),
                'status'             => $status,
            ]);

            // Add hours to volunteer total
            $log->volunteer->addApprovedHours($finalHours);

            // Mark attendance as verified
            $log->attendanceLog?->update([
                'status'      => 'verified',
                'verified_by' => $admin->id,
            ]);

            event(new HoursApproved($log));
        });
    }

    /**
     * Reject an hour log.
     */
    public function reject(HourLog $log, User $admin, string $reason): void
    {
        $log->update([
            'status'            => 'rejected',
            'adjustment_reason' => $reason,
            'approved_by'       => $admin->id,
            'approved_at'       => now(),
        ]);
    }
}
