<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Volunteer extends Model
{
    use HasFactory, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['status', 'total_approved_hours'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'user_id', 'name', 'email', 'phone',
        'date_of_birth', 'gender', 'address',
        'skills', 'interests', 'availability',
        'bio', 'emergency_contact', 'emergency_contact_name', 'emergency_contact_phone',
        'profile_photo', 'status', 'total_hours', 'total_approved_hours',
        'approved_at', 'approved_by', 'internal_notes',
    ];

    protected $casts = [
        'skills'       => 'array',
        'interests'    => 'array',
        'availability' => 'array',
        'date_of_birth'=> 'date',
        'approved_at'  => 'datetime',
        'total_hours'            => 'float',
        'total_approved_hours'   => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function hours()
    {
        return $this->hasMany(VolunteerHour::class);
    }

    public function hourLogs()
    {
        return $this->hasMany(HourLog::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function slotRequests()
    {
        return $this->hasMany(VolunteerSlotRequest::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(VolunteerSchedule::class, 'volunteer_schedule_user')
            ->withPivot(['hours_worked', 'status', 'notes'])
            ->withTimestamps();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'active']);
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getTotalHoursAttribute(): float
    {
        return (float) DB::table('volunteer_schedule_user')
            ->where('volunteer_id', $this->id)
            ->where('status', 'attended')
            ->sum('hours_worked');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? ($this->user?->name ?? 'Unknown Volunteer');
    }

    public function getIsApprovedAttribute(): bool
    {
        return in_array($this->status, ['approved', 'active']);
    }

    // ── Conflict Detection ─────────────────────────────────────────────────────

    public function hasShiftConflict(VolunteerShift $newShift): bool
    {
        return $this->slotRequests()
            ->where('status', 'approved')
            ->whereHas('shift', function ($q) use ($newShift) {
                $q->where('shift_date', $newShift->shift_date)
                  ->where(function ($q2) use ($newShift) {
                      $q2->whereBetween('start_time', [$newShift->start_time, $newShift->end_time])
                         ->orWhereBetween('end_time', [$newShift->start_time, $newShift->end_time])
                         ->orWhere(function ($q3) use ($newShift) {
                             $q3->where('start_time', '<=', $newShift->start_time)
                                ->where('end_time', '>=', $newShift->end_time);
                         });
                  });
            })->exists();
    }

    public function hasConflict(VolunteerSchedule $newSchedule): bool
    {
        return $this->schedules()
            ->where('event_date', $newSchedule->event_date)
            ->where(function ($q) use ($newSchedule) {
                $q->whereBetween('start_time', [$newSchedule->start_time, $newSchedule->end_time])
                  ->orWhereBetween('end_time', [$newSchedule->start_time, $newSchedule->end_time])
                  ->orWhere(function ($q2) use ($newSchedule) {
                      $q2->where('start_time', '<=', $newSchedule->start_time)
                         ->where('end_time', '>=', $newSchedule->end_time);
                  });
            })->exists();
    }

    // ── Business Methods ───────────────────────────────────────────────────────

    public function approve(User $admin): void
    {
        $this->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
    }

    public function reject(User $admin, string $reason = null): void
    {
        $this->update([
            'status'         => 'rejected',
            'internal_notes' => $reason,
        ]);
    }

    public function suspend(User $admin, string $reason = null): void
    {
        $this->update([
            'status'         => 'suspended',
            'internal_notes' => $reason,
        ]);
    }

    public function reactivate(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function addApprovedHours(float $hours): void
    {
        $this->increment('total_approved_hours', $hours);
    }
}
