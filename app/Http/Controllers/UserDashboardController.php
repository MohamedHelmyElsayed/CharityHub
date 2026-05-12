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
            'total_donated'      => Donation::where('user_id', $user->id)->completed()->sum('amount'),
            'donation_count'     => Donation::where('user_id', $user->id)->completed()->count(),
            'certificates_count' => Donation::where('user_id', $user->id)->whereNotNull('certificate_uuid')->count(),
            'impact_points'      => Donation::where('user_id', $user->id)->completed()->sum('amount') * 10,
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

    public function profile()
    {
        return view('pages.user-profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_delete' => ['required', 'in:DELETE'],
        ]);

        $user = auth()->user();

        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been permanently deleted.');
    }
}
