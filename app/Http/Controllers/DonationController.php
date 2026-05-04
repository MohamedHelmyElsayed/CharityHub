<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\Campaign;

class DonationController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function showDonatePage()
    {
        $campaigns = Campaign::where('status', 'active')->get();
        return view('pages.donate', compact('campaigns'));
    }

    public function createCheckoutSession(Request $request)
    {
        $amount = $request->amount;
        if ($amount === 'custom') {
            $amount = $request->custom_amount;
        }

        $request->merge(['final_amount' => $amount]);

        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'final_amount' => 'required|numeric|min:1',
            'recurring' => 'nullable',
        ]);

        $user = auth()->user();
        $campaign = Campaign::findOrFail($request->campaign_id);
        
        $session = $this->stripeService->createCheckoutSession(
            $user, 
            $campaign, 
            $request->final_amount, 
            $request->recurring == '1'
        );

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        // Stripe session ID is in $request->session_id
        return view('pages.donate-success');
    }

    public function cancel()
    {
        return view('pages.donate-cancel');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        return $this->stripeService->handleWebhook($payload, $sigHeader);
    }
}
