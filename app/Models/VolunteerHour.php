<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'volunteer_schedule_id',
        'date',
        'hours',
        'status',
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function schedule()
    {
        return $this->belongsTo(VolunteerSchedule::class, 'volunteer_schedule_id');
    }
}
