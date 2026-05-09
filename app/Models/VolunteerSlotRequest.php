<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerSlotRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'campaign_id',
        'requested_date',
        'requested_start_time',
        'requested_end_time',
        'notes',
        'status',
        'admin_notes',
        'completed_at',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'requested_start_time' => 'datetime',
        'requested_end_time' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
