<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Certificate;
use App\Models\Subscription;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_donated' => Donation::where('user_id', $user->id)->completed()->sum('amount'),
            'donation_count' => Donation::where('user_id', $user->id)->completed()->count(),
            'certificates_count' => Donation::where('user_id', $user->id)->whereNotNull('certificate_uuid')->count(),
            'impact_points' => Donation::where('user_id', $user->id)->completed()->sum('amount') * 10, // Example impact calculation
        ];

        $recentDonations = Donation::with('campaign')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $certificates = Donation::where('user_id', $user->id)
            ->whereNotNull('certificate_uuid')
            ->orderByDesc('created_at')
            ->get();

        $subscriptions = Subscription::with(['campaign'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('pages.user-dashboard', compact('stats', 'recentDonations', 'certificates', 'subscriptions'));
    }
}
