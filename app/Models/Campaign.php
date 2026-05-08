<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'cover_image',
        'image',
        'goal_amount',
        'current_amount',
        'deadline',
        'status',
        'featured',
        'og_title',
        'og_description',
        'lat',
        'long',
        'category',
    ];

    protected $casts = [
        'deadline' => 'date',
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Campaign $campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->title) . '-' . Str::random(6);
            }
        });
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function impactReports()
    {
        return $this->hasMany(ImpactReport::class);
    }

    public function volunteerSchedules()
    {
        return $this->hasMany(VolunteerSchedule::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->goal_amount <= 0) {
            return 0;
        }
        return min(100, (int) round(($this->current_amount / $this->goal_amount) * 100));
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) return null;
        $days = now()->diffInDays($this->deadline, false);
        return max(0, (int) $days);
    }

    public function getDonorCountAttribute(): int
    {
        return $this->donations()->where('status', 'completed')->distinct('donor_id')->count('donor_id');
    }

    public function getPublicUrlAttribute(): string
    {
        return route('campaigns.show', $this->slug);
    }

    public function getShareUrlAttribute(): string
    {
        return url('/campaigns/' . $this->slug);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
