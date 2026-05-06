<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Volunteer;
use App\Models\Donation;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_campaigns' => Campaign::active()->count(),
            'total_volunteers' => Volunteer::count(),
            'pending_donations' => Donation::where('status', 'pending')->count(),
            'recent_donations_count' => Donation::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentCampaigns = Campaign::orderByDesc('created_at')->limit(5)->get();
        $recentVolunteers = Volunteer::orderByDesc('created_at')->limit(5)->get();

        return view('pages.staff-dashboard', compact('stats', 'recentCampaigns', 'recentVolunteers'));
    }
}
