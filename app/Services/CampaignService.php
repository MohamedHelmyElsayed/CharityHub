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
