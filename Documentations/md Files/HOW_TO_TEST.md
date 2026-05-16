# CharityHub — How To Test

A complete guide to verifying all implemented features: automated tests, manual flows, and edge cases.

---

## 1. Prerequisites

Make sure your local environment is running:

```powershell
# XAMPP MySQL should be running on port 3307
# Start the Laravel dev server
cd c:\xampp\htdocs\CharityHub
php artisan serve
```

---

## 2. Seed the Database

Wipes and re-seeds with all demo data (5 campaigns, 3 donors, subscriptions, certificates, volunteers, impact reports):

```powershell
php artisan migrate:fresh --seed
```

**Expected output:**
```
✅ CharityHub demo data seeded successfully!
   Admin: admin@charityhub.org / password
   Staff: staff@charityhub.org / password
   Donor: alice@example.com / password
   Certificate verify URL: /verify/<uuid>
```

**Verify campaigns (should see 5):**
```powershell
php artisan tinker --execute="echo App\Models\Campaign::count() . ' campaigns';"
```

---

## 3. Run the Automated Test Suite

```powershell
cd c:\xampp\htdocs\CharityHub
php artisan test
```

Or run only the CharityHub tests:

```powershell
php artisan test --filter=CharityHubTest
```

### What Each Test Covers

| Test | What It Verifies |
|---|---|
| `test_successful_donation_fires_event_and_queues_jobs` | `DonationReceived` event is dispatched on a completed donation |
| `test_donation_received_event_dispatches_jobs` | Listener pushes `CertificateGenerationJob` to `certificates` queue and `LedgerEntryJob` to `ledger` queue |
| `test_failed_payment_creates_no_certificate` | A `failed` donation has no certificate record |
| `test_refunded_donation_revokes_certificate` | Refund sets donation `status=refunded` and certificate `status=revoked` |
| `test_subscription_creation` | Subscription record is created and `isActive()` returns true |
| `test_certificate_special_characters_no_error` | Names with Unicode/apostrophes (e.g. `José O'Brien-Müller`) store correctly |
| `test_certificate_large_amount` | Amounts up to 1,000,000 store and format correctly (`1000000.00`) |
| `test_verify_endpoint_valid_token` | `/verify/{uuid}` returns 200 with correct view data |
| `test_verify_endpoint_invalid_token_returns_404` | Invalid UUID returns 404 |
| `test_volunteer_conflict_detection` | Overlapping schedules on the same day are detected by `Volunteer::hasConflict()` |
| `test_volunteer_hour_calculation` | `total_hours` attribute sums `hours_worked` across attended schedules correctly |
| `test_financial_log_cannot_be_updated` | `FinancialLog::update()` throws `LogicException` (append-only audit trail) |
| `test_subscription_cancellation_lifecycle` | Setting `ends_at` to past + `stripe_status=canceled` → `isCancelled()=true`, `isActive()=false` |
| `test_recurring_renewal_creates_new_donation_and_fires_event` | Recurring renewal creates a new donation record and fires `DonationReceived` |
| `test_duplicate_idempotency_key_returns_409` | Posting to `/donate/checkout` with an existing idempotency key returns HTTP 409 |
| `test_fake_gateway_can_be_swapped_and_returns_checkout_url` | `FakePaymentGateway` can be injected via IoC and returns deterministic URLs |

**All 16 tests must be green.**

---

## 4. Payment Service Abstraction (Gateway Swapping)

The gateway is bound in `app/Providers/AppServiceProvider.php`:

```php
// Current binding → Paymob
$this->app->bind(PaymentGatewayInterface::class, \App\Services\PaymobGateway::class);

// To switch to Stripe, change to:
$this->app->bind(PaymentGatewayInterface::class, \App\Services\StripeGateway::class);
```

**To test the swap manually:**
```powershell
php artisan tinker
```
```php
$gateway = app(\App\Contracts\PaymentGatewayInterface::class);
echo get_class($gateway); // Should print PaymobGateway (or StripeGateway if swapped)
```

---

## 5. Simulate Donation Flows (Browser / Manual)

### 5a. Successful Donation

1. Navigate to `http://127.0.0.1:8000/donate`
2. Select a campaign, enter amount, fill in name + email, check GDPR consent
3. Submit → you will be redirected to the payment gateway
4. **Local shortcut**: In `APP_ENV=local`, the success page automatically marks the latest pending donation as `completed` and fires `DonationReceived` (see `DonationController::success`)
5. Navigate to `http://127.0.0.1:8000/donate/success`
6. Check the admin ledger at `http://127.0.0.1:8000/admin/ledger` to see the financial log entry

### 5b. Failed Donation

Simulate via Tinker:

```powershell
php artisan tinker
```
```php
$donation = App\Models\Donation::latest()->first();
$donation->update(['status' => 'failed']);

// Verify no certificate was generated
echo $donation->certificate_path; // null
echo App\Models\Certificate::where('donation_id', $donation->id)->count(); // 0
```

### 5c. Refund Flow

```php
$donation = App\Models\Donation::where('status', 'completed')->first();
$donation->update(['status' => 'refunded', 'refunded_at' => now()]);
$donation->certificate?->update(['status' => 'revoked']);

// Verify
echo $donation->fresh()->status;           // refunded
echo $donation->certificate->fresh()->status; // revoked
```

---

## 6. Recurring Subscription Lifecycle

### Create a subscription (Tinker):

```php
$donor    = App\Models\Donor::first();
$campaign = App\Models\Campaign::first();

$sub = App\Models\Subscription::create([
    'donor_id'      => $donor->id,
    'campaign_id'   => $campaign->id,
    'stripe_id'     => 'sub_test_manual_001',
    'stripe_status' => 'active',
    'amount'        => 25,
    'currency'      => 'USD',
]);

echo $sub->isActive();    // true
echo $sub->isCancelled(); // false
```

### Simulate monthly renewal:

```php
// This mimics handleInvoicePaid in DonationController
$renewal = App\Models\Donation::create([
    'donor_id'        => $sub->donor_id,
    'campaign_id'     => $sub->campaign_id,
    'amount'          => $sub->amount,
    'currency'        => $sub->currency,
    'type'            => 'recurring',
    'status'          => 'completed',
    'idempotency_key' => Illuminate\Support\Str::uuid(),
    'ip_address'      => null,
]);

event(new App\Events\DonationReceived($renewal));
// Check the financial log for a new entry:
echo App\Models\FinancialLog::where('donation_id', $renewal->id)->count(); // 1 (if QUEUE_CONNECTION=sync)
```

### Cancel the subscription:

```php
$sub->update(['stripe_status' => 'canceled', 'ends_at' => now()->subDay()]);
echo $sub->fresh()->isActive();    // false
echo $sub->fresh()->isCancelled(); // true
```

---

## 7. Certificate Generation Edge Cases

### Special characters in donor names:

```php
$donor = App\Models\Donor::create([
    'name'           => "José O'Brien-Müller",
    'email'          => 'test.unicode@example.com',
    'gdpr_consent'   => true,
    'gdpr_consent_at'=> now(),
]);
$campaign = App\Models\Campaign::first();
$donation = App\Models\Donation::create([
    'donor_id'        => $donor->id,
    'campaign_id'     => $campaign->id,
    'amount'          => 500,
    'currency'        => 'USD',
    'type'            => 'one_time',
    'status'          => 'completed',
    'idempotency_key' => Illuminate\Support\Str::uuid(),
]);

App\Jobs\CertificateGenerationJob::dispatch($donation);
// If QUEUE_CONNECTION=sync, certificate is generated immediately.
// PDF saved to storage/app/certificates/<uuid>.pdf
```

### Very large amounts:

```php
$donation = App\Models\Donation::create([
    'donor_id'        => $donor->id,
    'campaign_id'     => $campaign->id,
    'amount'          => 1000000,
    'currency'        => 'USD',
    'type'            => 'one_time',
    'status'          => 'completed',
    'idempotency_key' => Illuminate\Support\Str::uuid(),
]);

App\Jobs\CertificateGenerationJob::dispatch($donation);
// Certificate stores amount as 1000000.00 — verify with:
$cert = App\Models\Certificate::where('donation_id', $donation->id)->first();
echo number_format($cert->amount, 2, '.', ''); // 1000000.00
```

---

## 8. Volunteer Scheduling

### Test conflict detection:

```php
$volunteer = App\Models\Volunteer::first();
$schedule1 = App\Models\VolunteerSchedule::where('event_date', '>=', today())->first();

// Create an overlapping schedule
$overlap = App\Models\VolunteerSchedule::create([
    'event_name' => 'Overlap Test',
    'event_date' => $schedule1->event_date,
    'start_time' => $schedule1->start_time,
    'end_time'   => $schedule1->end_time,
    'status'     => 'scheduled',
    'campaign_id'=> $schedule1->campaign_id,
]);

$volunteer->schedules()->attach($schedule1->id, ['status' => 'registered']);
echo $volunteer->hasConflict($overlap) ? 'CONFLICT DETECTED ✅' : 'No conflict';
```

### Test hour calculation:

```php
$volunteer = App\Models\Volunteer::first();
echo $volunteer->total_hours; // Should sum hours_worked for all 'attended' pivot records
```

---

## 9. Financial Audit Trail

### Verify append-only (should throw):

```php
$log = App\Models\FinancialLog::first();
try {
    $log->update(['status' => 'modified']); // Throws LogicException
} catch (\LogicException $e) {
    echo 'Audit trail protected: ' . $e->getMessage();
}

try {
    $log->delete(); // Also throws
} catch (\LogicException $e) {
    echo 'Cannot delete: ' . $e->getMessage();
}
```

### View all financial logs:

```
http://127.0.0.1:8000/admin/ledger
```

---

## 10. Idempotency Duplicate Key Test

```powershell
# Using curl (PowerShell)
$ikey = [System.Guid]::NewGuid().ToString()

# First request — should succeed (redirect)
curl -X POST http://127.0.0.1:8000/donate/checkout `
  -d "campaign_id=1&amount=100&type=one_time&name=Test&email=t@t.com&gdpr_consent=1&idempotency_key=$ikey"

# Second request with same key — should return 409
curl -X POST http://127.0.0.1:8000/donate/checkout `
  -d "campaign_id=1&amount=100&type=one_time&name=Test&email=t@t.com&gdpr_consent=1&idempotency_key=$ikey"
# Response: {"error":"Duplicate request detected."}
```

---

## 11. Admin Panel

| URL | What to check |
|---|---|
| `/admin` | Dashboard — campaign totals, donation stats |
| `/admin/campaigns` | All 5 campaigns listed |
| `/admin/donations` | All donations with status (completed/failed/refunded) |
| `/admin/ledger` | Immutable financial log entries |
| `/admin/donors` | All donors with donation history |
| `/admin/volunteers` | Volunteer list with total hours |
| `/admin/volunteer-schedules` | Schedules with conflict indicators |

Login as admin: `admin@charityhub.org` / `password`
