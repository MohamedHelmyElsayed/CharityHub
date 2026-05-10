<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id', 'shift_id', 'check_in', 'check_out',
        'check_in_method', 'check_out_method', 'ip_address',
        'location_data', 'verified_by', 'status', 'notes',
    ];

    protected $casts = [
        'check_in'      => 'datetime',
        'check_out'     => 'datetime',
        'location_data' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function shift()
    {
        return $this->belongsTo(VolunteerShift::class, 'shift_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function hourLog()
    {
        return $this->hasOne(HourLog::class, 'attendance_log_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getCalculatedHoursAttribute(): float
    {
        if (!$this->check_out) return 0.0;
        return round($this->check_in->floatDiffInHours($this->check_out), 2);
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return !is_null($this->check_out);
    }
}
