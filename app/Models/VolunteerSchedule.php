<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'description',
        'location',
        'event_date',
        'start_time',
        'end_time',
        'max_volunteers',
        'status',
        'campaign_id',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function volunteers()
    {
        return $this->belongsToMany(Volunteer::class, 'volunteer_schedule_user')
            ->withPivot(['hours_worked', 'status', 'notes'])
            ->withTimestamps();
    }

    public function getHoursDurationAttribute(): float
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        return ($end - $start) / 3600;
    }

    public function isAtCapacity(): bool
    {
        if (!$this->max_volunteers) return false;
        return $this->volunteers()->wherePivot('status', '!=', 'cancelled')->count() >= $this->max_volunteers;
    }
}
