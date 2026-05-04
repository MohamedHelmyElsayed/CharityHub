<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use App\Jobs\DonorEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDonationConfirmation implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(DonationReceived $event): void
    {
        // Email is dispatched from CertificateGenerationJob after PDF is ready.
        // This listener is kept as a hook for immediate confirmation emails
        // (without attachment) if needed. Currently a no-op.
    }
}
