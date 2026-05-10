<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['status', 'amount', 'refunded_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'user_id',
        'donor_id',
        'campaign_id',
        'subscription_id',
        'amount',
        'currency',
        'type',
        'is_recurring',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_refund_id',
        'payment_id',
        'stripe_payment_intent_id',
        'idempotency_key',
        'anonymous',
        'message',
        'ip_address',
        'certificate_uuid',
        'certificate_path',
        'certificate_generated_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'anonymous' => 'boolean',
        'is_recurring' => 'boolean',
        'certificate_generated_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Donation $donation) {
            if (empty($donation->certificate_uuid)) {
                $donation->certificate_uuid = Str::uuid();
            }
        });
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

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    public function financialLogs()
    {
        return $this->hasMany(FinancialLog::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecurring($query)
    {
        return $query->where('type', 'recurring');
    }

    public function getDonorNameAttribute(): string
    {
        if ($this->anonymous) return 'Anonymous';
        return $this->donor?->name ?? $this->user?->name ?? 'Anonymous';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded' || $this->refunded_at !== null;
    }
}
