<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use App\Services\CampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCampaignProgress
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function handle(object $event): void
    {
        $donation = match (get_class($event)) {
            \App\Events\DonationReceived::class => $event->donation,
            \App\Events\SubscriptionRenewed::class => $event->donation,
            \App\Events\RefundIssued::class => $event->donation,
            default => null,
        };

        if ($donation && $donation->campaign) {
            $this->campaignService->updateProgress($donation->campaign, $donation->amount);
        }
    }
}
