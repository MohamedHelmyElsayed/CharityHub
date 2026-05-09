<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Events\DonationReceived;
use App\Jobs\LedgerEntryJob;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Subscription;
use App\Services\CampaignService;
use Illuminate\Http\Request;
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

        $campaign = Campaign::findOrFail($validated['campaign_id']);

        if ($campaign->status === 'ended') {
            return back()->with('error', 'This campaign has reached its goal and is no longer accepting donations.');
        }

        // Idempotency check
        $existing = Donation::where('idempotency_key', $validated['idempotency_key'])->first();
        if ($existing) {
            return response()->json(['error' => 'Duplicate request detected.'], 409);
        }

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
        } catch (\InvalidArgumentException $e) {
            \Illuminate\Support\Facades\Log::error('Webhook Signature Verification Failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }

        \Illuminate\Support\Facades\Log::info('Webhook Event Normalized', ['type' => $event['type']]);

        match ($event['type']) {
            'checkout.session.completed' => $this->handleSessionCompleted($event),
            'invoice.paid' => $this->handleInvoicePaid($event),
            'invoice.payment_failed' => $this->handlePaymentFailed($event),
            'charge.refunded' => $this->handleRefund($event),
            default => null,
        };

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

        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
        if (!$subscription) return;

        // Create a donation record for this renewal
        $donation = Donation::create([
            'donor_id' => $subscription->donor_id,
            'campaign_id' => $subscription->campaign_id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency ?? 'USD',
            'type' => 'recurring',
            'status' => 'completed',
            'idempotency_key' => Str::uuid(),
            'ip_address' => null,
        ]);

        event(new DonationReceived($donation));
    }

    private function handlePaymentFailed(array $event): void
    {
        $data = $event['data'];
        $subscriptionId = $data['subscription'] ?? null;

        $donation = Donation::where('idempotency_key', $data['metadata']['idempotency_key'] ?? '')->first();
        if ($donation) {
            $donation->update(['status' => 'failed']);
            LedgerEntryJob::dispatch($donation, 'donation', 'failed', $event['event_id'])->onQueue('ledger');
        }
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

        LedgerEntryJob::dispatch($donation, 'refund', 'success', $event['event_id'])->onQueue('ledger');
    }
}
