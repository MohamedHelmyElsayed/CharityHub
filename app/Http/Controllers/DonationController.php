<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Events\DonationReceived;
use App\Events\PaymentFailed;
use App\Events\RefundIssued;
use App\Events\WebhookReceived;
use App\Events\WebhookFailed;
use App\Jobs\LedgerEntryJob;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Subscription;
use App\Models\PaymentWebhook;
use App\Models\Refund;
use App\Events\SubscriptionCreated;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionRenewed;
use App\Events\RenewalFailed;
use App\Notifications\SubscriptionRenewedNotification;
use App\Notifications\PaymentFailedNotification;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly CampaignService $campaignService
    ) {}

    public function showDonatePage(Request $request)
    {
        $campaigns = Campaign::active()->orderByDesc('created_at')->get();
        $selectedCampaign = $request->campaign_id
            ? Campaign::find($request->campaign_id)
            : $campaigns->first();

        return view('pages.donate', compact('campaigns', 'selectedCampaign'));
    }

    public function createCheckoutSession(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1|max:1000000',
            'type' => 'required|in:one_time,recurring',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'anonymous' => 'boolean',
            'message' => 'nullable|string|max:500',
            'gdpr_consent' => 'required|accepted',
            'idempotency_key' => 'required|string|max:64',
        ]);

        if (auth()->user()->isAdmin()) {
            return back()->with('error', 'Admins cannot make donations.');
        }

        $campaign = Campaign::findOrFail($validated['campaign_id']);

        if ($campaign->status === 'ended') {
            return back()->with('error', 'This campaign has reached its goal and is no longer accepting donations.');
        }

        // Idempotency handled by middleware

        // Find or create donor
        $donor = Donor::firstOrCreate(
            ['email' => $validated['email']],
            [
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'anonymous' => $validated['anonymous'] ?? false,
                'gdpr_consent' => true,
                'gdpr_consent_at' => now(),
            ]
        );

        $idempotencyKey = $validated['idempotency_key'];
        $amount = (float) $validated['amount'];
        $currency = 'EGP';

        try {
            if ($validated['type'] === 'recurring') {
                $result = $this->gateway->createSubscription($donor, $campaign, $amount, $currency, $idempotencyKey);
            } else {
                $result = $this->gateway->createOneTimeCharge($donor, $campaign, $amount, $currency, $idempotencyKey);
            }

            // Create pending donation record
            Donation::create([
                'user_id' => auth()->id(),
                'donor_id' => $donor->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'currency' => $currency,
                'type' => $validated['type'],
                'status' => 'pending',
                'gateway' => $result['gateway'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'anonymous' => $validated['anonymous'] ?? false,
                'message' => $validated['message'] ?? null,
                'ip_address' => $request->ip(),
            ]);

            return redirect()->away($result['checkout_url']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Payment gateway error: ' . $e->getMessage())->withInput();
        }
    }

    public function success(Request $request)
    {
        // Stripe uses session_id, Paymob uses id in success redirect
        $sessionId = $request->get('session_id') ?? $request->get('id');

        if ($sessionId) {
            \Illuminate\Support\Facades\Log::info('Verifying payment on success page', ['session_id' => $sessionId]);
            
            $event = $this->gateway->verifyPayment($sessionId);
            
            if ($event && $event['type'] === 'checkout.session.completed') {
                $this->handleSessionCompleted($event);
            }
        }

        return view('pages.donate-success', compact('sessionId'));
    }

    public function cancel()
    {
        return view('pages.donate-cancel');
    }

    public function webhook(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Donation Webhook Received', [
            'method' => $request->method(),
            'query' => $request->query(),
            'payload_preview' => substr($request->getContent(), 0, 500)
        ]);

        $payload = $request->getContent();
        // Paymob sends hmac via query, Stripe sends via header
        $signature = $request->query('hmac', $request->header('Stripe-Signature', ''));

        try {
            $event = $this->gateway->handleWebhook($payload, $signature);
            event(new WebhookReceived(get_class($this->gateway), $event));
        } catch (\InvalidArgumentException $e) {
            \Illuminate\Support\Facades\Log::error('Webhook Signature Verification Failed', ['error' => $e->getMessage()]);
            event(new WebhookFailed(get_class($this->gateway), $e->getMessage(), ['payload' => $payload]));
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Idempotency check
        $webhook = PaymentWebhook::firstOrCreate(
            ['gateway_event_id' => $event['event_id']],
            [
                'gateway' => get_class($this->gateway),
                'event_type' => $event['type'],
                'payload' => $event['data'],
            ]
        );

        if ($webhook->processed_at) {
            return response()->json(['status' => 'already_processed']);
        }

        try {
            match ($event['type']) {
                'checkout.session.completed' => $this->handleSessionCompleted($event),
                'invoice.paid', 'invoice.payment_succeeded' => $this->handleInvoicePaid($event),
                'invoice.payment_failed' => $this->handlePaymentFailed($event),
                'charge.refunded' => $this->handleRefund($event),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
                default => null,
            };

            $webhook->update(['processed_at' => now()]);
        } catch (\Throwable $e) {
            $webhook->update(['error' => $e->getMessage()]);
            \Illuminate\Support\Facades\Log::error('Webhook processing failed', [
                'event_id' => $event['event_id'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleSessionCompleted(array $event): void
    {
        $data = $event['data'];
        $idempotencyKey = $data['metadata']['idempotency_key'] ?? null;

        \Illuminate\Support\Facades\Log::info('Handling Session Completed', ['idempotency_key' => $idempotencyKey]);

        $donation = Donation::where('idempotency_key', $idempotencyKey)->first();
        if (!$donation) {
            \Illuminate\Support\Facades\Log::warning('Donation not found for idempotency key', ['key' => $idempotencyKey]);
            return;
        }

        $updated = Donation::where('idempotency_key', $idempotencyKey)
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
                'stripe_payment_intent_id' => $data['payment_intent'] ?? null,
            ]);
        
        if ($updated) {
            $donation = Donation::where('idempotency_key', $idempotencyKey)->first();

            // If it's a recurring donation, create or update the Subscription record
            if ($donation->type === 'recurring' && !empty($data['subscription'])) {
                $subscription = Subscription::updateOrCreate(
                    ['gateway_subscription_id' => $data['subscription']],
                    [
                        'user_id' => $donation->user_id,
                        'donor_id' => $donation->donor_id,
                        'campaign_id' => $donation->campaign_id,
                        'gateway' => $event['gateway'] ?? 'stripe',
                        'gateway_customer_id' => $data['customer'] ?? null,
                        'amount' => $donation->amount,
                        'currency' => $donation->currency,
                        'status' => 'active',
                        'next_billing_date' => now()->addMonth(), // Default for monthly
                    ]
                );

                $donation->update([
                    'subscription_id' => $subscription->id,
                    'is_recurring' => true,
                    'gateway' => $event['gateway'] ?? 'stripe',
                    'gateway_transaction_id' => $data['payment_intent'] ?? null,
                ]);

                event(new SubscriptionCreated($subscription));
            } else {
                $donation->update([
                    'gateway' => $event['gateway'] ?? 'stripe',
                    'gateway_transaction_id' => $data['payment_intent'] ?? null,
                ]);
            }

            // Fire event → handles progress update, certificate generation, and ledger entry
            \Illuminate\Support\Facades\Log::info('Firing DonationReceived Event from Webhook', ['donation_id' => $donation->id]);
            event(new DonationReceived($donation));
        } else {
            \Illuminate\Support\Facades\Log::info('Donation already processed or not found', ['idempotency_key' => $idempotencyKey]);
        }
    }

    private function handleInvoicePaid(array $event): void
    {
        $data = $event['data'];
        $subscriptionId = $data['subscription'] ?? null;
        if (!$subscriptionId) return;

        $subscription = Subscription::where('gateway_subscription_id', $subscriptionId)->first();
        if (!$subscription) return;

        // Create a donation record for this renewal
        $donation = Donation::create([
            'user_id' => $subscription->user_id,
            'donor_id' => $subscription->donor_id,
            'campaign_id' => $subscription->campaign_id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency ?? 'USD',
            'type' => 'recurring',
            'is_recurring' => true,
            'status' => 'completed',
            'gateway' => 'stripe',
            'gateway_transaction_id' => $data['payment_intent'] ?? null,
            'idempotency_key' => 'renew_' . ($data['id'] ?? Str::random(10)),
            'ip_address' => null,
        ]);

        $subscription->update([
            'status' => 'active',
            'next_billing_date' => isset($data['lines']['data'][0]['period']['end']) 
                ? \Carbon\Carbon::createFromTimestamp($data['lines']['data'][0]['period']['end']) 
                : now()->addMonth(),
        ]);

        event(new DonationReceived($donation));
        event(new SubscriptionRenewed($subscription, $donation));

        // Notify Donor
        if ($subscription->user) {
            $subscription->user->notify(new SubscriptionRenewedNotification($subscription, $donation));
        }
    }

    private function handlePaymentFailed(array $event): void
    {
        $data = $event['data'];
        $subscriptionId = $data['subscription'] ?? null;
        if (!$subscriptionId) return;

        $subscription = Subscription::where('gateway_subscription_id', $subscriptionId)->first();
        if (!$subscription) return;

        $subscription->update(['status' => 'past_due']);

        // Create a failed donation record for audit
        $donation = Donation::create([
            'donor_id' => $subscription->donor_id,
            'campaign_id' => $subscription->campaign_id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency,
            'type' => 'recurring',
            'is_recurring' => true,
            'status' => 'failed',
            'idempotency_key' => 'fail_' . $event['event_id'],
            'gateway' => 'stripe',
        ]);

        event(new RenewalFailed($subscription, $data));
        event(new PaymentFailed($donation, $data));
    }

    private function handleRefund(array $event): void
    {
        $data = $event['data'];
        $paymentIntentId = $data['payment_intent'] ?? null;

        $donation = Donation::where('stripe_payment_intent_id', $paymentIntentId)->first();
        if (!$donation) return;

        $donation->update(['status' => 'refunded', 'refunded_at' => now()]);

        // Update certificate status
        $donation->certificate?->update(['status' => 'revoked']);

        // Reverse campaign amount
        $refundAmount = $data['amount_refunded'] / 100;
        \Illuminate\Support\Facades\DB::table('campaigns')
            ->where('id', $donation->campaign_id)
            ->decrement('current_amount', $refundAmount);

        event(new RefundIssued($donation, $event));

        // Create a Refund record
        Refund::create([
            'donation_id' => $donation->id,
            'amount' => $refundAmount,
            'currency' => $donation->currency,
            'reason' => $data['reason'] ?? 'Stripe Refund',
            'gateway_refund_id' => $data['id'] ?? null,
            'status' => 'completed',
        ]);
    }

    private function handleSubscriptionDeleted(array $event): void
    {
        $data = $event['data'];
        $subscription = Subscription::where('gateway_subscription_id', $data['id'])->first();
        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
                'ends_at' => isset($data['ended_at']) ? \Carbon\Carbon::createFromTimestamp($data['ended_at']) : now(),
            ]);
            event(new SubscriptionCancelled($subscription));
        }
    }

    private function handleSubscriptionUpdated(array $event): void
    {
        $data = $event['data'];
        $subscription = Subscription::where('gateway_subscription_id', $data['id'])->first();
        if ($subscription) {
            $subscription->update([
                'status' => $data['status'],
                'next_billing_date' => isset($data['current_period_end']) ? \Carbon\Carbon::createFromTimestamp($data['current_period_end']) : $subscription->next_billing_date,
            ]);
        }
    }
}
