<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Sum hours_worked from the pivot table for all attended events.
     * Uses a direct DB query for SQLite/MySQL compatibility.
     */
    public function getTotalHoursAttribute(): float
    {
        return (float) DB::table('volunteer_schedule_user')
            ->where('volunteer_id', $this->id)
            ->where('status', 'attended')
            ->sum('hours_worked');
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
