<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'current_amount',
        'deadline',
        'image',
        'status',
        'lat',
        'long',
    ];

    protected $casts = [
        'deadline' => 'date',
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->goal_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->current_amount / $this->goal_amount) * 100));
    }
}
