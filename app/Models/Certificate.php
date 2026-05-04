<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'donation_id',
        'donor_id',
        'donor_name',
        'amount',
        'campaign_title',
        'certificate_path',
        'status',
        'emailed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'emailed_at' => 'datetime',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function getVerifyUrlAttribute(): string
    {
        return route('verify.certificate', $this->uuid);
    }

    public function getMaskedDonorNameAttribute(): string
    {
        $name = $this->donor_name;
        $parts = explode(' ', $name);
        return collect($parts)->map(function ($part, $index) {
            if ($index === 0 && strlen($part) > 1) {
                return substr($part, 0, 1) . str_repeat('*', strlen($part) - 1);
            }
            return $part;
        })->implode(' ');
    }
}
