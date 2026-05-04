<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\FinancialLog;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    /**
     * Update campaign's current_amount after a successful donation.
     */
    public function updateProgress(Campaign $campaign, float $amount): void
    {
        DB::table('campaigns')
            ->where('id', $campaign->id)
            ->increment('current_amount', $amount);

        // Auto-end campaign if goal reached
        $campaign->refresh();
        if ($campaign->current_amount >= $campaign->goal_amount && $campaign->status === 'active') {
            $campaign->update(['status' => 'ended']);
        }
    }

    /**
     * Get live donation feed (recent completed donations for a campaign).
     */
    public function getLiveFeed(Campaign $campaign, int $limit = 10): \Illuminate\Support\Collection
    {
        return Donation::with(['donor'])
            ->where('campaign_id', $campaign->id)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($d) => [
                'name' => $d->anonymous ? 'Anonymous' : ($d->donor?->name ?? 'Anonymous'),
                'amount' => (float) $d->amount,
                'time' => $d->created_at->diffForHumans(),
                'message' => $d->message,
            ]);
    }

    /**
     * Get all campaigns.
     */
    public function getAllCampaigns(): \Illuminate\Database\Eloquent\Collection
    {
        return Campaign::latest()->get();
    }

    /**
     * Get a single campaign by ID.
     */
    public function getCampaignById(int $id): Campaign
    {
        return Campaign::findOrFail($id);
    }

    /**
     * Create a new campaign.
     */
    public function createCampaign(array $data): Campaign
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['featured_image'] = $data['image']->store('campaigns', 'public');
            unset($data['image']);
        }

        $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
        
        return Campaign::create($data);
    }

    /**
     * Update an existing campaign.
     */
    public function updateCampaign(int $id, array $data): Campaign
    {
        $campaign = Campaign::findOrFail($id);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['featured_image'] = $data['image']->store('campaigns', 'public');
            unset($data['image']);
        }

        if (isset($data['title']) && $data['title'] !== $campaign->title) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
        }

        $campaign->update($data);

        return $campaign;
    }

    /**
     * Delete a campaign.
     */
    public function deleteCampaign(int $id): bool
    {
        return Campaign::findOrFail($id)->delete();
    }

    /**
     * Get stats for admin dashboard.
     */
    public function getDashboardStats(): array
    {
        return [
            'total_raised' => Donation::where('status', 'completed')->sum('amount'),
            'total_donors' => Donor::count(),
            'active_campaigns' => Campaign::active()->count(),
            'total_donations' => Donation::where('status', 'completed')->count(),
        ];
    }
}
