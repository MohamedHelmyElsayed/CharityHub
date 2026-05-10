<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'key',
        'request_hash',
        'response_payload',
        'status',
        'ip_address',
    ];

    protected $casts = [
        'response_payload' => 'array',
    ];
}
