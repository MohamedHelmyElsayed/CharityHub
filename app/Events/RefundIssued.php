<?php

namespace App\Events;

use App\Models\Donation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundIssued
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Donation $donation,
        public readonly array $gatewayResponse = []
    ) {}
}
