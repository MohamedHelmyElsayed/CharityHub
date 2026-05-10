<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VolunteerShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'title', 'description', 'shift_date', 'start_time',
        'end_time', 'required_volunteers', 'assigned_count', 'location',
        'qr_code', 'qr_token', 'qr_expires_at', 'status',
    ];

    protected $casts = [
        'shift_date'    => 'date',
        'qr_expires_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(VolunteerEvent::class, 'event_id');
    }

    public function slotRequests()
    {
        return $this->hasMany(VolunteerSlotRequest::class, 'shift_id');
    }

    public function approvedRequests()
    {
        return $this->hasMany(VolunteerSlotRequest::class, 'shift_id')->where('status', 'approved');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'shift_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('shift_date', '>=', today())->where('status', 'open');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getIsFullAttribute(): bool
    {
        return $this->assigned_count >= $this->required_volunteers;
    }

    public function getDurationHoursAttribute(): float
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end   = \Carbon\Carbon::parse($this->end_time);
        return round($start->floatDiffInHours($end), 2);
    }

    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->required_volunteers - $this->assigned_count);
    }

    // ── Methods ────────────────────────────────────────────────────────────────

    public function generateQrToken(): string
    {
        $token = Str::random(64);
        $this->update([
            'qr_token'      => $token,
            'qr_expires_at' => now()->addHours(24),
        ]);
        return $token;
    }

    public function isQrTokenValid(string $token): bool
    {
        return $this->qr_token === $token
            && $this->qr_expires_at
            && now()->lt($this->qr_expires_at);
    }

    public function incrementAssignedCount(): void
    {
        $this->increment('assigned_count');
        if ($this->assigned_count >= $this->required_volunteers) {
            $this->update(['status' => 'full']);
        }
    }

    public function decrementAssignedCount(): void
    {
        if ($this->assigned_count > 0) {
            $this->decrement('assigned_count');
        }
        if ($this->status === 'full' && $this->assigned_count < $this->required_volunteers) {
            $this->update(['status' => 'open']);
        }
    }
}
