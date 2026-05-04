<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Campaign;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createOneTimeCharge(
        Donor $donor,
        Campaign $campaign,
        float $amount,
        string $currency,
        string $idempotencyKey
    ): array {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => ['name' => "Donation to: {$campaign->title}"],
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('donate.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('donate.cancel'),
            'customer_email' => $donor->email,
            'metadata' => [
                'donor_id' => $donor->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'idempotency_key' => $idempotencyKey,
                'type' => 'one_time',
            ],
        ], [
            'idempotency_key' => $idempotencyKey,
        ]);

        return ['checkout_url' => $session->url, 'session_id' => $session->id];
    }

    public function createSubscription(
        Donor $donor,
        Campaign $campaign,
        float $amount,
        string $currency,
        string $idempotencyKey
    ): array {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => ['name' => "Monthly Donation — {$campaign->title}"],
                    'unit_amount' => (int) round($amount * 100),
                    'recurring' => ['interval' => 'month'],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('donate.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('donate.cancel'),
            'customer_email' => $donor->email,
            'metadata' => [
                'donor_id' => $donor->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'idempotency_key' => $idempotencyKey,
                'type' => 'recurring',
            ],
        ], [
            'idempotency_key' => $idempotencyKey,
        ]);

        return ['checkout_url' => $session->url, 'session_id' => $session->id];
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            \Stripe\Subscription::cancel($subscriptionId);
            return true;
        } catch (\Throwable $e) {
            Log::error('Stripe subscription cancellation failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function handleWebhook(string $payload, string $signature): array
    {
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            throw new \InvalidArgumentException('Invalid Stripe webhook payload');
        } catch (SignatureVerificationException $e) {
            throw new \InvalidArgumentException('Invalid Stripe webhook signature');
        }

        return [
            'type' => $event->type,
            'event_id' => $event->id,
            'data' => $event->data->object->toArray(),
        ];
    }
}
