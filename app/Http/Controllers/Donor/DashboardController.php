<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Donation;
use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use App\Events\SubscriptionCancelled;

class DashboardController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway
    ) {}

    public function index()
    {
        $user = auth()->user();
        $subscriptions = Subscription::where('user_id', $user->id)
            ->with(['campaign', 'donations'])
            ->orderByDesc('created_at')
            ->get();

        $donations = Donation::where('user_id', $user->id)
            ->with('campaign')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('donor.dashboard', compact('subscriptions', 'donations'));
    }

    public function cancelSubscription(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        if ($subscription->isCancelled()) {
            return back()->with('error', 'Subscription is already cancelled.');
        }

        $success = $this->gateway->cancelSubscription($subscription->gateway_subscription_id);

        if ($success) {
            $subscription->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
            ]);

            event(new SubscriptionCancelled($subscription));

            return back()->with('success', 'Your subscription has been cancelled successfully.');
        }

        return back()->with('error', 'Failed to cancel subscription. Please contact support.');
    }

    public function donationHistory()
    {
        $donations = Donation::where('user_id', auth()->id())
            ->with('campaign')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('donor.donations', compact('donations'));
    }
}
