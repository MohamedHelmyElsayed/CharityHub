<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerSlotRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id', 'shift_id', 'campaign_id',
        'requested_date', 'requested_start_time', 'requested_end_time',
        'notes', 'admin_notes', 'status',
        'requested_at', 'approved_at', 'approved_by',
        'rejected_at', 'rejected_by', 'rejection_reason', 'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at'  => 'datetime',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
        'cancelled_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'requested_date'=> 'date',
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

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
