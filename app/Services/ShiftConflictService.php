<?php

namespace App\Services;

use App\Models\Volunteer;
use App\Models\VolunteerApplication;
use App\Models\VolunteerShift;
use App\Models\VolunteerSlotRequest;

class ShiftConflictService
{
    /**
     * Run all validations. Returns null if OK, error string if blocked.
     */
    public function validate(Volunteer $volunteer, VolunteerShift $shift): ?string
    {
        if ($this->isShiftFull($shift)) {
            return 'This shift is already at full capacity.';
        }
        if ($this->isRegistrationExpired($shift)) {
            return 'The registration deadline for this opportunity has passed.';
        }
        if ($this->hasDuplicateRequest($volunteer, $shift)) {
            return 'You already have a pending or approved request for this shift.';
        }
        if (!$this->isApprovedForOpportunity($volunteer, $shift)) {
            return 'You must be approved for this opportunity before requesting a shift.';
        }
        if ($conflict = $this->findConflict($volunteer, $shift)) {
            $other = $conflict->shift;
            return "This shift overlaps with '{$other?->event?->title} — {$other?->title}' "
                 . "({$other?->start_time} – {$other?->end_time}) on the same day.";
        }
        return null;
    }

    /**
     * Check if volunteer has an approved/pending slot that overlaps with the given shift.
     */
    public function hasConflict(Volunteer $volunteer, VolunteerShift $shift): bool
    {
        return $this->findConflict($volunteer, $shift) !== null;
    }

    /**
     * Return the first conflicting VolunteerSlotRequest (with shift.event loaded).
     */
    public function findConflict(Volunteer $volunteer, VolunteerShift $shift): ?VolunteerSlotRequest
    {
        return $volunteer->slotRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('shift', function ($q) use ($shift) {
                $q->where('shift_date', $shift->shift_date)
                  ->where('id', '!=', $shift->id)
                  ->where(function ($q2) use ($shift) {
                      $q2->where('start_time', '<', $shift->end_time)
                         ->where('end_time', '>', $shift->start_time);
                  });
            })
            ->with('shift.event')
            ->first();
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
        if (!$event || !$event->registration_deadline) return false;
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
     * Volunteer must have an approved application for the opportunity that owns this shift.
     */
    public function isApprovedForOpportunity(Volunteer $volunteer, VolunteerShift $shift): bool
    {
        return VolunteerApplication::where('user_id', $volunteer->user_id)
            ->where('event_id', $shift->event_id)
            ->whereIn('status', ['approved', 'pending'])
            ->exists();
    }
}
