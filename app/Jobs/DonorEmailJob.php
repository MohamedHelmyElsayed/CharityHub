<?php

namespace App\Jobs;

use App\Mail\DonationCertificateMail;
use App\Models\Certificate;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DonorEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly Donation $donation
    ) {}

    public function handle(): void
    {
        $donation = $this->donation->fresh(['donor', 'campaign', 'certificate']);
        if (!$donation) return;

        $email = $donation->donor?->email ?? $donation->user?->email;
        if (!$email) return;

        $certificate = $donation->certificate;

        Mail::to($email)->send(new DonationCertificateMail($donation, $certificate));

        if ($certificate) {
            $certificate->update([
                'status' => 'emailed',
                'emailed_at' => now(),
            ]);
        }
    }
}
