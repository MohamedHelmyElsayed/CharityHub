<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(User $user, Campaign $campaign, $amount, $recurring = false)
    {
        $lineItems = [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => "Donation to: {$campaign->title}",
                ],
                'unit_amount' => $amount * 100, // Amount in cents
                'recurring' => $recurring ? ['interval' => 'month'] : null,
            ],
            'quantity' => 1,
        ]];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => $recurring ? 'subscription' : 'payment',
            'success_url' => route('donate.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('donate.cancel'),
            'customer_email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'recurring' => $recurring ? '1' : '0',
            ],
        ]);

        return $session;
    }

    public function handleWebhook($payload, $sigHeader)
    {
        $endpointSecret = config('services.stripe.webhook_secret');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->processCompletedSession($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->processSubscriptionPayment($event->data->object);
                break;
            // Add more cases for subscription lifecycle (canceled, updated, etc)
        }

        return response()->json(['status' => 'success']);
    }

    protected function processCompletedSession($session)
    {
        $metadata = $session->metadata;
        
        if ($session->mode === 'payment') {
            $this->createDonation($metadata->user_id, $metadata->campaign_id, $metadata->amount, $session->payment_intent);
        } elseif ($session->mode === 'subscription') {
            $this->createSubscription($metadata->user_id, $session->subscription, $session->customer);
        }
    }

    protected function createDonation($userId, $campaignId, $amount, $paymentId)
    {
        $donation = Donation::create([
            'user_id' => $userId,
            'campaign_id' => $campaignId,
            'amount' => $amount,
            'status' => 'completed',
            'payment_id' => $paymentId,
        ]);

        // Trigger Progress Update
        app(CampaignService::class)->updateProgress($campaignId, $amount);

        // Log financial transaction
        Log::channel('financial')->info("Donation Received: ID {$donation->id}, User {$userId}, Amount {$amount}");

        // Dispatch Event (DonationReceived)
        event(new \App\Events\DonationReceived($donation));
    }

    protected function createSubscription($userId, $subscriptionId, $customerId)
    {
        $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);
        
        Subscription::updateOrCreate(
            ['stripe_id' => $subscriptionId],
            [
                'user_id' => $userId,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $stripeSubscription->items->data[0]->price->id,
                'quantity' => $stripeSubscription->items->data[0]->quantity,
                'trial_ends_at' => $stripeSubscription->trial_end ? date('Y-m-d H:i:s', $stripeSubscription->trial_end) : null,
                'ends_at' => null,
            ]
        );
    }

    protected function processSubscriptionPayment($invoice)
    {
        if ($invoice->subscription) {
            $subscription = Subscription::where('stripe_id', $invoice->subscription)->first();
            if ($subscription) {
                // You might want to create a Donation record for each recurring payment too
                // But for simplicity, we just log it or update the user's total impact
            }
        }
    }
}
