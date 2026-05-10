<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $gateway,
        public readonly string $reason,
        public readonly array $payload = []
    ) {}
}
