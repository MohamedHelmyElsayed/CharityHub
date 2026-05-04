<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'date',
        'hours',
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}
