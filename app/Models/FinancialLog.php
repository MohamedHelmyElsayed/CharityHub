<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FinancialLog extends Model
{
    use HasFactory;

    /**
     * Financial logs are append-only. No updates allowed.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'donor_id',
        'donation_id',
        'campaign_id',
        'transaction_type',
        'amount',
        'currency',
        'status',
        'gateway',
        'gateway_transaction_id',
        'idempotency_key',
        'metadata',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'hash',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Prevent updates to maintain audit trail integrity.
     */
    protected static function booted(): void
    {
        static::creating(function (FinancialLog $log) {
            $log->user_id = $log->user_id ?? Auth::id();
            $log->ip_address = $log->ip_address ?? request()->ip();
            $log->user_agent = $log->user_agent ?? request()->userAgent();
            $log->hash = $log->calculateHash();
        });

        static::updating(function () {
            throw new \LogicException('Financial logs are append-only and cannot be updated.');
        });

        static::deleting(function () {
            throw new \LogicException('Financial logs cannot be deleted for audit trail integrity.');
        });
    }

    /**
     * Calculate a cryptographic hash of the record to detect tampering.
     */
    public function calculateHash(): string
    {
        $fields = ['transaction_type', 'amount', 'currency', 'gateway_transaction_id', 'idempotency_key', 'status'];
        $data = [];
        foreach ($fields as $field) {
            $val = $this->{$field};
            if ($field === 'amount') {
                $val = number_format((float)$val, 2, '.', '');
            }
            $data[$field] = (string)$val;
        }

        return hash_hmac('sha256', json_encode($data), config('app.key') ?? 'base64:CharityHubAuditSecretKey');
    }

    /**
     * Verify if the record has been tampered with.
     */
    public function verifyHash(): bool
    {
        return hash_equals($this->hash, $this->calculateHash());
    }
}
