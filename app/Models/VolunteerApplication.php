<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'user_id',
        'motivation', 'skills_offered', 'experience', 'availability', 'notes',
        'status', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(VolunteerEvent::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'warning',
        };
    }

    // ── Business Methods ───────────────────────────────────────────────────────

    public function approve(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'status'      => 'approved',
            'admin_notes' => $notes,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'status'      => 'rejected',
            'admin_notes' => $notes,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
        ]);
    }
}
