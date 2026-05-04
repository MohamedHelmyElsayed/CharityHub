<?php

namespace App\Contracts;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Campaign;

interface PaymentGatewayInterface
{
    /**
     * Create a one-time charge checkout session.
     * Returns a redirect URL to the payment gateway checkout.
     */
    public function createOneTimeCharge(
        Donor $donor,
        Campaign $campaign,
        float $amount,
        string $currency,
        string $idempotencyKey
    ): array;

    /**
     * Create a recurring monthly subscription.
     * Returns a redirect URL to the payment gateway checkout.
     */
    public function createSubscription(
        Donor $donor,
        Campaign $campaign,
        float $amount,
        string $currency,
        string $idempotencyKey
    ): array;

    /**
     * Cancel an active subscription.
     */
    public function cancelSubscription(string $subscriptionId): bool;

    /**
     * Handle incoming webhook payload from the payment gateway.
     * Returns a normalized event array with type and data.
     */
    public function handleWebhook(string $payload, string $signature): array;
}
