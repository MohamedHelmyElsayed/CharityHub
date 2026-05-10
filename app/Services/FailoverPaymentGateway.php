<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Donor;
use App\Models\Campaign;
use Illuminate\Support\Facades\Log;

class FailoverPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @param StripeGateway $stripe
     * @param PaymobGateway $paymob
     */
    public function __construct(
        private readonly StripeGateway $stripe,
        private readonly PaymobGateway $paymob
    ) {}

    /**
     * Try Stripe first, fall back to Paymob on failure.
     */
    public function createOneTimeCharge(Donor $donor, Campaign $campaign, float $amount, string $currency, string $idempotencyKey): array
    {
        try {
            $result = $this->stripe->createOneTimeCharge($donor, $campaign, $amount, $currency, $idempotencyKey);
            $result['gateway'] = 'stripe';
            return $result;
        } catch (\Throwable $e) {
            Log::warning('Stripe one-time charge failed, falling back to Paymob', [
                'donor_email' => $donor->email,
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);
            $result = $this->paymob->createOneTimeCharge($donor, $campaign, $amount, $currency, $idempotencyKey);
            $result['gateway'] = 'paymob';
            return $result;
        }
    }

    /**
     * Try Stripe first, fall back to Paymob on failure.
     */
    public function createSubscription(Donor $donor, Campaign $campaign, float $amount, string $currency, string $idempotencyKey): array
    {
        try {
            $result = $this->stripe->createSubscription($donor, $campaign, $amount, $currency, $idempotencyKey);
            $result['gateway'] = 'stripe';
            return $result;
        } catch (\Throwable $e) {
            Log::warning('Stripe subscription failed, falling back to Paymob', [
                'donor_email' => $donor->email,
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);
            $result = $this->paymob->createSubscription($donor, $campaign, $amount, $currency, $idempotencyKey);
            $result['gateway'] = 'paymob';
            return $result;
        }
    }

    /**
     * Attempts to cancel subscription on Stripe first, then Paymob.
     */
    public function cancelSubscription(?string $subscriptionId): bool
    {
        if ($this->stripe->cancelSubscription($subscriptionId)) {
            return true;
        }
        return $this->paymob->cancelSubscription($subscriptionId);
    }

    /**
     * Determines which gateway the webhook belongs to by trying signatures sequentially.
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            // Try Stripe verification
            $event = $this->stripe->handleWebhook($payload, $signature);
            $event['gateway'] = 'stripe';
            return $event;
        } catch (\InvalidArgumentException $e) {
            // If Stripe signature fails, it might be a Paymob webhook
            Log::info('Webhook signature not matching Stripe, trying Paymob...');
            $event = $this->paymob->handleWebhook($payload, $signature);
            $event['gateway'] = 'paymob';
            return $event;
        }
    }

    public function verifyPayment(string $sessionId): ?array
    {
        // Try Stripe first
        $result = $this->stripe->verifyPayment($sessionId);
        if ($result) {
            $result['gateway'] = 'stripe';
            return $result;
        }

        // Then try Paymob
        $result = $this->paymob->verifyPayment($sessionId);
        if ($result) {
            $result['gateway'] = 'paymob';
            return $result;
        }
        
        return null;
    }

    public function refundCharge(string $paymentIntentId, float $amount, ?string $reason = null): array
    {
        // Try Stripe first
        $result = $this->stripe->refundCharge($paymentIntentId, $amount, $reason);
        if ($result['status'] === 'success') {
            return $result;
        }

        // Then try Paymob
        return $this->paymob->refundCharge($paymentIntentId, $amount, $reason);
    }

    public function getSubscription(?string $subscriptionId): ?array
    {
        // Try Stripe first
        $result = $this->stripe->getSubscription($subscriptionId);
        if ($result) {
            return $result;
        }

        // Then try Paymob
        return $this->paymob->getSubscription($subscriptionId);
    }
}
