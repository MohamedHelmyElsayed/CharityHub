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

    public function handle(DonationReceived $event): void
    {
        $donation = $event->donation;
        $campaign = $donation->campaign;

        if ($campaign) {
            $this->campaignService->updateProgress($campaign, $donation->amount);
        }
    }
}
