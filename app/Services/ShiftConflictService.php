<?php

namespace App\Services;

use App\Models\Volunteer;
use App\Models\VolunteerShift;
use App\Models\VolunteerSlotRequest;

class ShiftConflictService
{
    /**
     * Check if volunteer has an approved slot that overlaps with the given shift.
     */
    public function hasConflict(Volunteer $volunteer, VolunteerShift $shift): bool
    {
        return $volunteer->slotRequests()
            ->where('status', 'approved')
            ->whereHas('shift', function ($q) use ($shift) {
                $q->where('shift_date', $shift->shift_date)
                  ->where('id', '!=', $shift->id)
                  ->where(function ($q2) use ($shift) {
                      // Overlaps if: existing.start < new.end AND existing.end > new.start
                      $q2->where('start_time', '<', $shift->end_time)
                         ->where('end_time', '>', $shift->start_time);
                  });
            })->exists();
    }

    /**
     * Check if the shift is already at capacity.
     */
    public function isShiftFull(VolunteerShift $shift): bool
    {
        return $shift->assigned_count >= $shift->required_volunteers;
    }

    /**
     * Check if registration deadline has passed.
     */
    public function isRegistrationExpired(VolunteerShift $shift): bool
    {
        $event = $shift->event;
        if (!$event || !$event->registration_deadline) {
            return false;
        }
        return now()->gt($event->registration_deadline);
    }

    /**
     * Check if the volunteer already has a pending/approved request for this shift.
     */
    public function hasDuplicateRequest(Volunteer $volunteer, VolunteerShift $shift): bool
    {
        return VolunteerSlotRequest::where('volunteer_id', $volunteer->id)
            ->where('shift_id', $shift->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
    }

    /**
     * Run all validations and return array of errors (empty = OK).
     */
    public function validate(Volunteer $volunteer, VolunteerShift $shift): array
    {
        $errors = [];

        if ($this->isShiftFull($shift)) {
            $errors[] = 'This shift is already at full capacity.';
        }
        if ($this->isRegistrationExpired($shift)) {
            $errors[] = 'The registration deadline for this event has passed.';
        }
        if ($this->hasDuplicateRequest($volunteer, $shift)) {
            $errors[] = 'You already have a pending or approved request for this shift.';
        }
        if ($this->hasConflict($volunteer, $shift)) {
            $errors[] = 'This shift overlaps with another shift you are already assigned to.';
        }
        if (!$volunteer->is_approved) {
            $errors[] = 'Your volunteer profile must be approved before requesting shifts.';
        }

        return $errors;
    }
}
