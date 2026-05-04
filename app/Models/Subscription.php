<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'donor_id',
        'campaign_id',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'amount',
        'currency',
        'trial_ends_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function isActive(): bool
    {
        return in_array($this->stripe_status, ['active', 'trialing']);
    }

    public function isCancelled(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }
}
