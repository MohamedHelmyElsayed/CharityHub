<?php

namespace App\Events;

use App\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RenewalFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly array $errorPayload = []
    ) {}
}
