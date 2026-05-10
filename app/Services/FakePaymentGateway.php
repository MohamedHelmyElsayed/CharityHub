<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Campaign;
use App\Models\Donor;

/**
 * FakePaymentGateway — Test-only implementation of PaymentGatewayInterface.
 *
 * Bind this in tests via:
 *   $this->app->bind(PaymentGatewayInterface::class, FakePaymentGateway::class);
 *
 * Never bind in production.
 */
class FakePaymentGateway implements PaymentGatewayInterface
{
    /** Controls whether createOneTimeCharge / createSubscription throw */
    public static bool $shouldFail = false;

    /** Stores calls for assertions in tests */
    public static array $capturedCharges = [];
    public static array $capturedSubscriptions = [];
    public static array $cancelledSubscriptions = [];

    public static function reset(): void
    {
        static::$shouldFail = false;
        static::$capturedCharges = [];
        static::$capturedSubscriptions = [];
        static::$cancelledSubscriptions = [];
    }

    public function createOneTimeCharge(
        Donor    $donor,
        Campaign $campaign,
        float    $amount,
        string   $currency,
        string   $idempotencyKey
    ): array {
        if (static::$shouldFail) {
            throw new \RuntimeException('FakePaymentGateway: simulated charge failure.');
        }

        static::$capturedCharges[] = compact('donor', 'campaign', 'amount', 'currency', 'idempotencyKey');

        return [
            'checkout_url' => route('donate.success') . '?session_id=fake_session_' . $idempotencyKey,
            'session_id'   => 'fake_session_' . $idempotencyKey,
        ];
    }

    public function createSubscription(
        Donor    $donor,
        Campaign $campaign,
        float    $amount,
        string   $currency,
        string   $idempotencyKey
    ): array {
        if (static::$shouldFail) {
            throw new \RuntimeException('FakePaymentGateway: simulated subscription failure.');
        }

        static::$capturedSubscriptions[] = compact('donor', 'campaign', 'amount', 'currency', 'idempotencyKey');

        return [
            'checkout_url' => route('donate.success') . '?session_id=fake_sub_' . $idempotencyKey,
            'session_id'   => 'fake_sub_' . $idempotencyKey,
        ];
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        static::$cancelledSubscriptions[] = $subscriptionId;
        return true;
    }

    /**
     * Accept any payload; return a normalized event array.
     * Pass `{"type":"checkout.session.completed","idempotency_key":"xxx"}` as payload.
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('FakePaymentGateway: invalid JSON webhook payload.');
        }

        $type = $data['type'] ?? 'unknown';

        return [
            'type'     => $type,
            'event_id' => $data['event_id'] ?? ('fake_evt_' . uniqid()),
            'data'     => [
                'payment_intent' => $data['payment_intent'] ?? 'fake_pi_' . uniqid(),
                'subscription'   => $data['subscription'] ?? null,
                'amount_refunded' => ($data['amount_refunded'] ?? 0) * 100,
                'metadata'       => [
                    'idempotency_key' => $data['idempotency_key'] ?? null,
                ],
            ],
        ];
    }

    /**
     * Verify a payment session directly with the gateway.
     * For testing, we assume any session starting with 'fake_session_' or 'fake_sub_' is valid.
     */
    public function verifyPayment(string $sessionId): ?array
    {
        $idempotencyKey = null;

        if (str_starts_with($sessionId, 'fake_session_')) {
            $idempotencyKey = str_replace('fake_session_', '', $sessionId);
        } elseif (str_starts_with($sessionId, 'fake_sub_')) {
            $idempotencyKey = str_replace('fake_sub_', '', $sessionId);
        }

        if ($idempotencyKey) {
            return [
                'type' => 'checkout.session.completed',
                'event_id' => 'fake_v_' . uniqid(),
                'data' => [
                    'payment_intent' => 'fake_pi_' . uniqid(),
                    'metadata' => [
                        'idempotency_key' => $idempotencyKey,
                    ],
                ],
            ];
        }

        return null;
    }
}
