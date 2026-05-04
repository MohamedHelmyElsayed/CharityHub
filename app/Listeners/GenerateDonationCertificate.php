<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use App\Jobs\CertificateGenerationJob;
use App\Jobs\LedgerEntryJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateDonationCertificate implements ShouldQueue
{
    public string $queue = 'certificates';

    public function handle(DonationReceived $event): void
    {
        $donation = $event->donation;

        // Dispatch certificate generation
        CertificateGenerationJob::dispatch($donation)->onQueue('certificates');

        // Dispatch ledger entry
        LedgerEntryJob::dispatch($donation, 'donation', 'success')->onQueue('ledger');
    }
}
