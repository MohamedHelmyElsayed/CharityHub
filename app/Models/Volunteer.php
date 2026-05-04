<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'skills',
        'bio',
        'emergency_contact',
        'status',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hours()
    {
        return $this->hasMany(VolunteerHour::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(VolunteerSchedule::class, 'volunteer_schedule_user')
            ->withPivot(['hours_worked', 'status', 'notes'])
            ->withTimestamps();
    }

    public function getTotalHoursAttribute(): float
    {
        return (float) $this->schedules()
            ->wherePivot('status', 'attended')
            ->sum('volunteer_schedule_user.hours_worked');
    }

    /**
     * Detect scheduling conflicts: check if volunteer is already booked
     * for another event that overlaps with the given date/time range.
     */
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
            })
            ->exists();
    }
}
