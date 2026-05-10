<?php

namespace App\Jobs;

use App\Models\Donation;
use App\Models\FinancialLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LedgerEntryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(
        public readonly Donation $donation,
        public readonly string $type = 'donation',
        public readonly string $status = 'success',
        public readonly ?string $stripeEventId = null,
        public readonly array $metadata = []
    ) {}

    public function handle(): void
    {
        $donation = $this->donation->fresh(['donor', 'campaign']);
        if (!$donation) return;

        FinancialLog::create([
            'donor_id' => $donation->donor_id,
            'campaign_id' => $donation->campaign_id,
            'donation_id' => $donation->id,
            'amount' => $donation->amount,
            'currency' => $donation->currency ?? 'USD',
            'transaction_type' => $this->type,
            'gateway_transaction_id' => $this->stripeEventId,
            'status' => $this->status,
            'metadata' => array_merge([
                'donation_type' => $donation->type,
                'campaign_title' => $donation->campaign?->title,
                'donor_name' => $donation->anonymous ? 'Anonymous' : ($donation->donor?->name ?? 'Unknown'),
            ], $this->metadata),
            'ip_address' => $donation->ip_address,
        ]);
    }
}
