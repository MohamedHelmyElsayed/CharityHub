<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignProgress extends Component
{
    public int $campaignId;
    public ?Campaign $campaign = null;

    public function mount(int $campaignId): void
    {
        $this->campaignId = $campaignId;
        $this->campaign = Campaign::find($campaignId);
    }

    public function refreshData(): void
    {
        $this->campaign = Campaign::find($this->campaignId);
    }

    public function render()
    {
        return view('livewire.campaign-progress');
    }
}
