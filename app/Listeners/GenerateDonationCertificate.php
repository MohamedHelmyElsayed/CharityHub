<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use App\Jobs\CertificateGenerationJob;
use App\Jobs\LedgerEntryJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateDonationCertificate implements ShouldQueue
{
    public string $queue = 'certificates';

    public function handle(object $event): void
    {
        $donation = match (get_class($event)) {
            \App\Events\DonationReceived::class => $event->donation,
            \App\Events\SubscriptionRenewed::class => $event->donation,
            default => null,
        };

        if ($donation) {
            CertificateGenerationJob::dispatch($donation)->onQueue('certificates');
        }
    }
}
