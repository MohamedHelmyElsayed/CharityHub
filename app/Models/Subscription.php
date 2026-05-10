<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Subscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'donor_id',
        'campaign_id',
        'gateway',
        'gateway_subscription_id',
        'gateway_customer_id',
        'gateway_plan_id',
        'status',
        'quantity',
        'amount',
        'currency',
        'billing_interval',
        'next_billing_date',
        'trial_ends_at',
        'ends_at',
        'cancelled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_billing_date' => 'datetime',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'next_billing_date', 'cancelled_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null || $this->status === 'canceled';
    }
}
