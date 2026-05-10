<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'user_id',
        'amount',
        'currency',
        'reason',
        'gateway_refund_id',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
