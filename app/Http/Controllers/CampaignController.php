<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ImpactReport;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::active();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $campaigns = $query->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('pages.campaigns', compact('campaigns'));
    }

    public function show(string $slug)
    {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        $recentDonations = $campaign->donations()
            ->with(['donor'])
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $impactReports = $campaign->impactReports()->published()->latest()->get();

        return view('pages.campaign-details', compact('campaign', 'recentDonations', 'impactReports'));
    }
}
