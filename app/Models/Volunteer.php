<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hours()
    {
        return $this->hasMany(VolunteerHour::class);
    }

    public function getTotalHoursAttribute()
    {
        return $this->hours()->sum('hours');
    }
}
