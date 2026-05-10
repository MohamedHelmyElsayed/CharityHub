<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'anonymous',
        'gdpr_consent',
        'gdpr_consent_at',
        'marketing_opt_in',
    ];

    protected $casts = [
        'anonymous' => 'boolean',
        'gdpr_consent' => 'boolean',
        'gdpr_consent_at' => 'datetime',
        'marketing_opt_in' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function financialLogs()
    {
        return $this->hasMany(FinancialLog::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->anonymous ? 'Anonymous' : $this->name;
    }

    public function getMaskedNameAttribute(): string
    {
        if ($this->anonymous) {
            return 'Anonymous';
        }
        $parts = explode(' ', $this->name);
        return collect($parts)->map(function ($part, $index) {
            if ($index === 0) return substr($part, 0, 1) . str_repeat('*', max(strlen($part) - 1, 1));
            return $part; // Keep last name visible
        })->implode(' ');
    }

    public function getTotalDonatedAttribute(): float
    {
        return (float) $this->donations()->where('status', 'completed')->sum('amount');
    }

    /**
     * GDPR: Anonymize donor data for erasure requests.
     */
    public function anonymizeForGdpr(): void
    {
        $this->update([
            'name' => 'Anonymized',
            'email' => 'anonymized_' . $this->id . '@deleted.local',
            'phone' => null,
            'address' => null,
            'city' => null,
            'country' => null,
            'anonymous' => true,
        ]);
        $this->delete();
    }
}
