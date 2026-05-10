<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Campaign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobGateway implements PaymentGatewayInterface
{
    private ?string $apiKey;
    private ?string $integrationId;
    private ?string $iframeId;
    private ?string $hmacSecret;
    private string $baseUrl = 'https://accept.paymob.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
    }

    public function createOneTimeCharge(
        Donor $donor,
        Campaign $campaign,
        float $amount,
        string $currency,
        string $idempotencyKey
    ): array {
        // Step 1: Authentication Request
        $token = $this->authenticate();

        // Step 2: Order Registration Request
        $orderId = $this->registerOrder($token, $amount, $currency, "Donation to: {$campaign->title}", $idempotencyKey);

        // Step 3: Payment Key Request
        $paymentKey = $this->getPaymentKey($token, $amount, $currency, $orderId, $donor, [
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'idempotency_key' => $idempotencyKey,
            'type' => 'one_time',
        ]);

        // Step 4: Construct Iframe URL
        $checkoutUrl = "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";

        return ['checkout_url' => $checkoutUrl, 'session_id' => (string) $orderId];
    }

    public function createSubscription(
        Donor $donor,
        Campaign $campaign,
        float $amount,
        string $currency,
        string $idempotencyKey
    ): array {
        // Paymob subscription/recurring logic is similar but requires a tokenized card.
        // For simplicity in this implementation, we return a standard payment key flow
        // and would handle recurring natively or via Paymob's recurring APIs.
        return $this->createOneTimeCharge($donor, $campaign, $amount, $currency, $idempotencyKey);
    }

    public function cancelSubscription(?string $subscriptionId): bool
    {
        // Paymob recurring is often managed via a different dashboard or requires 
        // manual intervention for tokenized cards. We log it for admin awareness.
        Log::info('Manual PayMob subscription cancellation requested', ['subscription_id' => $subscriptionId]);
        return true;
    }

    public function handleWebhook(string $payload, string $signature): array
    {
        $data = json_decode($payload, true);
        
        // HMAC verification
        if (!$this->verifyHmac($data, $signature)) {
            throw new \InvalidArgumentException('Invalid Paymob webhook signature');
        }

        $type = $data['type'] ?? '';
        $obj = $data['obj'] ?? [];

        // Map Paymob events to the existing Stripe-like format for DonationController
        $normalizedType = match ($type) {
            'TRANSACTION' => $obj['success'] ? 'checkout.session.completed' : 'invoice.payment_failed',
            default => 'unknown',
        };

        return [
            'type' => $normalizedType,
            'event_id' => (string) ($obj['id'] ?? uniqid()),
            'data' => [
                'payment_intent' => (string) ($obj['id'] ?? ''),
                'metadata' => [
                    'idempotency_key' => $obj['order']['merchant_order_id'] ?? ($obj['payment_key_claims']['extra']['idempotency_key'] ?? null)
                ]
            ],
        ];
    }

    private function authenticate(): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Paymob API Key is missing. Please set PAYMOB_API_KEY in your .env file.');
        }

        if (empty($this->integrationId)) {
            throw new \Exception('Paymob Integration ID is missing. Please set PAYMOB_INTEGRATION_ID in your .env file.');
        }

        if (empty($this->iframeId)) {
            throw new \Exception('Paymob Iframe ID is missing. Please set PAYMOB_IFRAME_ID in your .env file.');
        }

        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey
        ]);

        if (!$response->successful() || !isset($response['token'])) {
            throw new \Exception('Paymob Authentication Failed: ' . $response->body());
        }

        return $response['token'];
    }

    private function registerOrder(string $token, float $amount, string $currency, string $name, string $merchantOrderId): int
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'merchant_order_id' => $merchantOrderId,
            'amount_cents' => (int) round($amount * 100),
            'currency' => $currency,
            'items' => [
                [
                    'name' => $name,
                    'amount_cents' => (int) round($amount * 100),
                    'description' => $name,
                    'quantity' => '1'
                ]
            ],
        ]);

        if (!$response->successful() || !isset($response['id'])) {
            throw new \Exception('Paymob Order Registration Failed: ' . $response->body());
        }

        return $response['id'];
    }

    private function getPaymentKey(string $token, float $amount, string $currency, int $orderId, Donor $donor, array $metadata): string
    {
        $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
            'auth_token' => $token,
            'amount_cents' => (int) round($amount * 100),
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'apartment' => 'NA',
                'email' => $donor->email,
                'floor' => 'NA',
                'first_name' => explode(' ', $donor->name)[0] ?? 'Donor',
                'street' => 'NA',
                'building' => 'NA',
                'phone_number' => '+201000000000',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'NA',
                'country' => 'NA',
                'last_name' => explode(' ', $donor->name)[1] ?? 'Donor',
                'state' => 'NA'
            ],
            'currency' => $currency,
            'integration_id' => $this->integrationId,
            'lock_order_when_paid' => 'false',
            'extra' => $metadata
        ]);

        if (!$response->successful() || !isset($response['token'])) {
            throw new \Exception('Paymob Payment Key Generation Failed: ' . $response->body());
        }

        return $response['token'];
    }

    public function verifyHmac(array $data, string $hmacHeader): bool
    {
        $obj = $data['obj'];
        $amount_cents = $obj['amount_cents'] ?? '';
        $created_at = $obj['created_at'] ?? '';
        $currency = $obj['currency'] ?? '';
        $error_occured = ($obj['error_occured'] ?? false) ? 'true' : 'false';
        $has_parent_transaction = ($obj['has_parent_transaction'] ?? false) ? 'true' : 'false';
        $id = $obj['id'] ?? '';
        $integration_id = $obj['integration_id'] ?? '';
        $is_3d_secure = ($obj['is_3d_secure'] ?? false) ? 'true' : 'false';
        $is_auth = ($obj['is_auth'] ?? false) ? 'true' : 'false';
        $is_capture = ($obj['is_capture'] ?? false) ? 'true' : 'false';
        $is_refunded = ($obj['is_refunded'] ?? false) ? 'true' : 'false';
        $is_standalone_payment = ($obj['is_standalone_payment'] ?? false) ? 'true' : 'false';
        $is_voided = ($obj['is_voided'] ?? false) ? 'true' : 'false';
        $order_id = $obj['order']['id'] ?? '';
        $owner = $obj['owner'] ?? '';
        $pending = ($obj['pending'] ?? false) ? 'true' : 'false';
        $source_data_pan = $obj['source_data']['pan'] ?? '';
        $source_data_sub_type = $obj['source_data']['sub_type'] ?? '';
        $source_data_type = $obj['source_data']['type'] ?? '';
        $success = ($obj['success'] ?? false) ? 'true' : 'false';

        $concatenatedString = $amount_cents . $created_at . $currency . $error_occured . 
                              $has_parent_transaction . $id . $integration_id . $is_3d_secure . 
                              $is_auth . $is_capture . $is_refunded . $is_standalone_payment . 
                              $is_voided . $order_id . $owner . $pending . $source_data_pan . 
                              $source_data_sub_type . $source_data_type . $success;

        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $this->hmacSecret);

        // Sometimes Paymob HMAC comparison needs to be lowercased
        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function verifyPayment(string $sessionId): ?array
    {
        try {
            $token = $this->authenticate();
            
            // In Paymob, the sessionId we returned in createOneTimeCharge was the Order ID.
            // However, the success redirect might contain the Transaction ID as 'id'.
            // If the sessionId looks like an order ID, we should try to find its transactions.
            // For now, assume sessionId is the Transaction ID if it's coming from redirect,
            // or we try to retrieve the transaction by its ID.
            
            $response = Http::withToken($token)->get("{$this->baseUrl}/acceptance/transactions/{$sessionId}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] === true && $data['pending'] === false) {
                    return [
                        'type' => 'checkout.session.completed',
                        'event_id' => 'v_' . $data['id'],
                        'data' => [
                            'payment_intent' => (string) $data['id'],
                            'metadata' => [
                                'idempotency_key' => $data['order']['merchant_order_id'] ?? null
                            ]
                        ],
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Paymob payment verification failed', ['id' => $sessionId, 'error' => $e->getMessage()]);
        }

        return null;
    }

    public function refundCharge(string $paymentIntentId, float $amount, ?string $reason = null): array
    {
        try {
            $token = $this->authenticate();
            
            $response = Http::post("{$this->baseUrl}/acceptance/void_refund/refund", [
                'auth_token' => $token,
                'transaction_id' => $paymentIntentId,
                'amount_cents' => (int) round($amount * 100),
            ]);

            $data = $response->json();

            // Paymob often returns 'success' => true or simply an object if successful
            if ($response->successful() && (!isset($data['success']) || $data['success'] === true) && isset($data['id'])) {
                return [
                    'status' => 'success',
                    'gateway_refund_id' => (string) $data['id'],
                    'data' => $data,
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Paymob refund failed: ' . ($data['detail'] ?? 'Unknown error from gateway'),
            ];
        } catch (\Throwable $e) {
            Log::error('Paymob refund exception', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Paymob refund exception: ' . $e->getMessage(),
            ];
        }
    }

    public function getSubscription(?string $subscriptionId): ?array
    {
        return null;
    }
}
