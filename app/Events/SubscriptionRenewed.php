<?php

namespace App\Events;

use App\Models\Donation;
use App\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly Donation $donation
    ) {}
}
