<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class FeaturedCampaigns extends Component
{
    public function render()
    {
        $campaigns = Campaign::active()
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('livewire.featured-campaigns', [
            'campaigns' => $campaigns,
        ]);
    }
}
