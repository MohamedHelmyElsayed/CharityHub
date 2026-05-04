<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // append-only, no updates allowed

    protected $fillable = [
        'donor_id',
        'campaign_id',
        'donation_id',
        'amount',
        'currency',
        'type',
        'stripe_event_id',
        'status',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    // Prevent updates to maintain audit trail integrity
    public static function boot(): void
    {
        parent::boot();
        static::updating(function () {
            throw new \LogicException('Financial logs are append-only and cannot be updated.');
        });
        static::deleting(function () {
            throw new \LogicException('Financial logs cannot be deleted for audit trail integrity.');
        });
    }
}
