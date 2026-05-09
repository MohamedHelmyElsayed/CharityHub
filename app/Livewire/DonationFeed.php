<?php

namespace App\Livewire;

use App\Models\Donation;
use Livewire\Component;

class DonationFeed extends Component
{
    public int $campaignId;
    public array $donations = [];

    public function mount(int $campaignId): void
    {
        $this->campaignId = $campaignId;
        $this->loadDonations();
    }

    public function loadDonations(): void
    {
        $this->donations = Donation::with(['donor'])
            ->where('campaign_id', $this->campaignId)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($d) => [
                'name' => $d->anonymous ? 'Anonymous' : ($d->donor?->name ?? 'Anonymous'),
                'amount' => (float) $d->amount,
                'time' => $d->created_at->diffForHumans(),
                'message' => $d->message,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.donation-feed');
    }
}
