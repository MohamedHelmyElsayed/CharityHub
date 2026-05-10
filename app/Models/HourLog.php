<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id', 'attendance_log_id', 'calculated_hours',
        'approved_hours', 'adjustment_reason', 'approved_by', 'approved_at', 'status',
    ];

    protected $casts = [
        'approved_at'        => 'datetime',
        'calculated_hours'   => 'float',
        'approved_hours'     => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class, 'attendance_log_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getFinalHoursAttribute(): float
    {
        return $this->approved_hours ?? $this->calculated_hours;
    }
}
