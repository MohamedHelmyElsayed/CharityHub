<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id', 'created_by', 'title', 'slug', 'description',
        'location', 'latitude', 'longitude', 'event_type', 'category',
        'required_skills', 'max_volunteers', 'cover_image', 'banner_image',
        'gallery', 'registration_deadline', 'start_date', 'end_date', 'status',
        'requirements', 'benefits',
    ];

    protected $casts = [
        'required_skills'       => 'array',
        'gallery'               => 'array',
        'start_date'            => 'datetime',
        'end_date'              => 'datetime',
        'registration_deadline' => 'datetime',
        'latitude'              => 'float',
        'longitude'             => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shifts()
    {
        return $this->hasMany(VolunteerShift::class, 'event_id');
    }

    public function applications()
    {
        return $this->hasMany(VolunteerApplication::class, 'event_id');
    }

    public function approvedApplications()
    {
        return $this->hasMany(VolunteerApplication::class, 'event_id')->where('status', 'approved');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())->where('status', 'open');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getTotalAssignedAttribute(): int
    {
        return $this->shifts()->sum('assigned_count');
    }

    public function getIsFullAttribute(): bool
    {
        if ($this->max_volunteers === 0) return false;
        return $this->total_assigned >= $this->max_volunteers;
    }

    public function getIsRegistrationOpenAttribute(): bool
    {
        if (!$this->registration_deadline) return $this->status === 'open';
        return $this->status === 'open' && now()->lt($this->registration_deadline);
    }

    // ── Route model binding ────────────────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
