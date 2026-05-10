<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use App\Events\PaymentFailed;
use App\Events\RefundIssued;
use App\Events\SubscriptionRenewed;
use App\Events\SubscriptionCreated;
use App\Events\SubscriptionCancelled;
use App\Events\RenewalFailed;
use App\Events\WebhookReceived;
use App\Events\WebhookFailed;
use App\Models\FinancialLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

class LogFinancialTransaction implements ShouldQueue
{
    public $queue = 'ledger';

    public function handle(object $event): void
    {
        $data = match (get_class($event)) {
            DonationReceived::class => [
                'donor_id' => $event->donation->donor_id,
                'donation_id' => $event->donation->id,
                'campaign_id' => $event->donation->campaign_id,
                'transaction_type' => 'payment_success',
                'amount' => $event->donation->amount,
                'currency' => $event->donation->currency,
                'status' => 'success',
                'gateway_transaction_id' => $event->donation->gateway_transaction_id ?? null,
                'idempotency_key' => $event->donation->idempotency_key,
            ],
            PaymentFailed::class => [
                'donor_id' => $event->donation->donor_id,
                'donation_id' => $event->donation->id,
                'transaction_type' => 'payment_failed',
                'amount' => $event->donation->amount,
                'currency' => $event->donation->currency,
                'status' => 'failed',
                'metadata' => $this->maskSensitiveData($event->gatewayResponse),
            ],
            RefundIssued::class => [
                'donor_id' => $event->donation->donor_id,
                'donation_id' => $event->donation->id,
                'transaction_type' => 'refund_issued',
                'amount' => $event->donation->amount,
                'currency' => $event->donation->currency,
                'status' => 'refunded',
                'metadata' => $this->maskSensitiveData($event->gatewayResponse),
            ],
            SubscriptionRenewed::class => [
                'donor_id' => $event->subscription->donor_id,
                'donation_id' => $event->donation->id,
                'transaction_type' => 'subscription_renewed',
                'amount' => $event->donation->amount,
                'currency' => $event->donation->currency,
                'status' => 'success',
            ],
            WebhookReceived::class => [
                'transaction_type' => 'webhook_received',
                'gateway' => $event->gateway,
                'status' => 'success',
                'metadata' => $this->maskSensitiveData($event->payload),
            ],
            WebhookFailed::class => [
                'transaction_type' => 'webhook_failed',
                'gateway' => $event->gateway,
                'status' => 'failed',
                'metadata' => [
                    'reason' => $event->reason,
                    'payload' => $this->maskSensitiveData($event->payload),
                ],
            ],
            SubscriptionCreated::class => [
                'donor_id' => $event->subscription->donor_id,
                'campaign_id' => $event->subscription->campaign_id,
                'transaction_type' => 'subscription_created',
                'amount' => $event->subscription->amount,
                'currency' => $event->subscription->currency,
                'status' => 'success',
                'gateway' => $event->subscription->gateway,
            ],
            SubscriptionCancelled::class => [
                'donor_id' => $event->subscription->donor_id,
                'campaign_id' => $event->subscription->campaign_id,
                'transaction_type' => 'subscription_cancelled',
                'status' => 'cancelled',
                'gateway' => $event->subscription->gateway,
            ],
            RenewalFailed::class => [
                'donor_id' => $event->subscription->donor_id,
                'campaign_id' => $event->subscription->campaign_id,
                'transaction_type' => 'renewal_failed',
                'amount' => $event->subscription->amount,
                'currency' => $event->subscription->currency,
                'status' => 'failed',
                'gateway' => $event->subscription->gateway,
                'metadata' => $this->maskSensitiveData($event->errorPayload),
            ],
            default => null,
        };

        if ($data) {
            FinancialLog::create($data);
        }
    }

    private function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = ['card', 'cvc', 'secret', 'token', 'api_key', 'password', 'number'];
        
        return $this->recursiveMask($data, $sensitiveKeys);
    }

    private function recursiveMask(array $data, array $sensitiveKeys): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->recursiveMask($value, $sensitiveKeys);
            } else {
                foreach ($sensitiveKeys as $sensitiveKey) {
                    if (str_contains(strtolower($key), $sensitiveKey)) {
                        $data[$key] = '********';
                        break;
                    }
                }
            }
        }
        return $data;
    }
}
