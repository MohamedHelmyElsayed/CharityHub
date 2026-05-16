# Payment Flow — CharityHub

## Architecture

```
User ──> DonationController ──> PaymentGatewayInterface
                                    │
                            ┌───────┴───────┐
                            │               │
                      StripeGateway    PaymobGateway
                            │               │
                      FailoverPaymentGateway (default binding)
```

**Default binding:** `FailoverPaymentGateway` wraps Stripe (primary) + Paymob (fallback).

---

## Donation Flow

```
User submits /donate form
    │
    ▼
POST /donate/checkout  [middleware: auth, idempotency]
    │
    ├── HandleIdempotency middleware checks X-Idempotency-Key
    │   • New key → process
    │   • Replayed key with same params → replay cached response
    │   • Collision (same key, different params) → 409 Conflict
    │
    ├── createCheckoutSession()
    │   • Validates input (campaign_id, amount, type, gdpr_consent, etc.)
    │   • Blocks admins from donating
    │   • Checks campaign is not ended
    │   • Creates/retrieves Donor (firstOrCreate by email)
    │   • Calls gateway->createOneTimeCharge() or createSubscription()
    │     └── Failover: tries Stripe → falls back to Paymob on exception
    │   • Creates pending Donation record
    │   • Redirects user to gateway checkout URL
    │
    ▼
User on Stripe Checkout / Paymob Iframe
    │
    ├── User completes payment
    │
    ▼
Redirect to GET /donate/success?session_id=xxx
    │
    ├── success()
    │   • gateway->verifyPayment(session_id)
    │   • If valid: handleSessionCompleted() (inline confirmation)
    │   • Renders success page
    │
    ▼
Gateway sends POST /stripe/webhook  [CSRF excluded]
    │
    ├── webhook()
    │   • Extract signature (Stripe-Signature header / ?hmac= query)
    │   • gateway->handleWebhook(payload, signature)
    │     └── Failover: tries Stripe → falls back to Paymob
    │   • PaymentWebhook::firstOrCreate (idempotent by gateway_event_id)
    │   • Match event type → handler:
    │
    │   checkout.session.completed
    │     → mark donation completed
    │     → create Subscription record if recurring
    │     → fire DonationReceived
    │
    │   invoice.paid / invoice.payment_succeeded
    │     → create renewal donation
    │     → update subscription next_billing_date
    │     → fire DonationReceived + SubscriptionRenewed
    │     → send renewal notification
    │
    │   invoice.payment_failed
    │     → mark subscription past_due
    │     → create failed donation record
    │     → fire RenewalFailed + PaymentFailed
    │
    │   charge.refunded
    │     → mark donation refunded
    │     → revoke certificate
    │     → reverse campaign amount
    │     → fire RefundIssued
    │
    │   customer.subscription.deleted
    │     → mark subscription canceled
    │     → fire SubscriptionCancelled
    │
    │   customer.subscription.updated
    │     → sync subscription status + dates
    │
    ▼
Event Listeners (queued)
    │
    ├── DonationReceived
    │   • UpdateCampaignProgress (sync — recalculates total from DB)
    │   • GenerateDonationCertificate (queued) → PDF → email
    │   • LogFinancialTransaction (queued)
    │
    ├── RefundIssued
    │   • UpdateCampaignProgress (sync)
    │   • LogFinancialTransaction (queued)
    │
    └── Other events → LogFinancialTransaction
```

---

## Idempotency Safeguards

| Layer | Mechanism |
|-------|-----------|
| Client | `crypto.randomUUID()` → hidden `idempotency_key` field |
| HTTP | `HandleIdempotency` middleware — replays cached success, blocks collisions |
| Gateway | Stripe-level idempotency key passed to Stripe API |
| Webhook | `PaymentWebhook::firstOrCreate` by `gateway_event_id` |
| Campaign progress | `CampaignService::updateProgress()` recalculates SUM — never increments |

---

## Gateway Implementations

### StripeGateway
- One-time: Stripe Checkout Session (mode=payment)
- Recurring: Stripe Checkout Session (mode=subscription)
- Webhook verification via `Stripe\Webhook::constructEvent()`
- Refund via `\Stripe\Refund::create()`

### PaymobGateway
- One-time: Auth → Register Order → Payment Key → Iframe URL
- Recurring: Delegates to one-time (not fully implemented)
- Webhook HMAC verification (20-field concatenation, sha512)
- Refund via Paymob Accept API

### FailoverPaymentGateway
- Wraps both gateways
- Tries Stripe first → catches Throwable → falls back to Paymob
- Webhook: tries Stripe sig → if fails, tries Paymob HMAC

---

## Subscription Lifecycle

```
checkout.session.completed (with subscription)
    → Subscription created, status=active
    → SubscriptionCreated event

invoice.paid (monthly)
    → Renewal donation created
    → SubscriptionRenewed event + email

invoice.payment_failed
    → Subscription status=past_due
    → RenewalFailed + PaymentFailed events

customer.subscription.deleted (user cancels in Stripe)
    → Subscription status=canceled
    → SubscriptionCancelled event

User cancels from Donor Dashboard
    → gateway->cancelSubscription()
    → Subscription status=canceled
    → SubscriptionCancelled event
```
