<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Volunteer;
use App\Models\VolunteerHour;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getSystemOverview()
    {
        return [
            'total_donations' => Donation::where('status', 'completed')->sum('amount'),
            'total_campaigns' => Campaign::count(),
            'total_volunteers' => Volunteer::count(),
            'total_volunteer_hours' => VolunteerHour::sum('hours'),
            'active_campaigns' => Campaign::where('status', 'active')->count(),
        ];
    }

    public function getCampaignImpact($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        
        return [
            'total_donated' => $campaign->current_amount,
            'goal_amount' => $campaign->goal_amount,
            'donors_count' => $campaign->donations()->where('status', 'completed')->distinct('user_id')->count(),
            'progress' => $campaign->progress_percentage,
        ];
    }

    public function getDonationTrends()
    {
        return Donation::where('status', 'completed')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();
    }
}
