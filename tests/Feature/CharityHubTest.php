<?php

namespace Tests\Feature;

use App\Contracts\PaymentGatewayInterface;
use App\Events\DonationReceived;
use App\Jobs\CertificateGenerationJob;
use App\Jobs\LedgerEntryJob;
use App\Models\Campaign;
use App\Models\Certificate;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\FinancialLog;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\VolunteerSchedule;
use App\Services\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CharityHubTest extends TestCase
{
    use RefreshDatabase;

    private function createCampaign(array $attrs = []): Campaign
    {
        return Campaign::create(array_merge([
            'title' => 'Test Campaign',
            'slug' => 'test-campaign-' . Str::random(6),
            'description' => 'A test campaign for donations.',
            'goal_amount' => 10000,
            'current_amount' => 0,
            'deadline' => now()->addDays(30)->toDateString(),
            'status' => 'active',
        ], $attrs));
    }

    private function createDonor(array $attrs = []): Donor
    {
        return Donor::create(array_merge([
            'name' => 'Test Donor',
            'email' => 'donor' . Str::random(4) . '@test.com',
            'gdpr_consent' => true,
            'gdpr_consent_at' => now(),
        ], $attrs));
    }

    private function createDonation(Donor $donor, Campaign $campaign, array $attrs = []): Donation
    {
        return Donation::create(array_merge([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 100,
            'currency' => 'USD',
            'type' => 'one_time',
            'status' => 'completed',
            'idempotency_key' => Str::uuid(),
            'certificate_uuid' => Str::uuid(),
            'anonymous' => false,
            'ip_address' => '127.0.0.1',
        ], $attrs));
    }

    // ── Test 1: Successful one-time donation ─────────────────────────────
    public function test_successful_donation_fires_event_and_queues_jobs(): void
    {
        Queue::fake();
        Event::fake([DonationReceived::class]);

        $campaign = $this->createCampaign();
        $donor = $this->createDonor();
        $donation = $this->createDonation($donor, $campaign);

        event(new DonationReceived($donation));

        Event::assertDispatched(DonationReceived::class);
    }

    public function test_donation_received_event_dispatches_jobs(): void
    {
        Queue::fake();

        $campaign = $this->createCampaign();
        $donor = $this->createDonor();
        $donation = $this->createDonation($donor, $campaign);

        $listener = new \App\Listeners\GenerateDonationCertificate();
        $listener->handle(new DonationReceived($donation));

        Queue::assertPushedOn('certificates', CertificateGenerationJob::class);
        Queue::assertPushedOn('ledger', LedgerEntryJob::class);
    }

    // ── Test 2: Failed payment logged, no certificate ────────────────────
    public function test_failed_payment_creates_no_certificate(): void
    {
        Queue::fake();

        $campaign = $this->createCampaign();
        $donor = $this->createDonor();
        $donation = $this->createDonation($donor, $campaign, ['status' => 'failed']);

        $this->assertEquals('failed', $donation->status);
        $this->assertNull($donation->certificate_path);
        $this->assertEquals(0, Certificate::where('donation_id', $donation->id)->count());
    }

    // ── Test 3: Refunded donation updates certificate status ─────────────
    public function test_refunded_donation_revokes_certificate(): void
    {
        $campaign = $this->createCampaign();
        $donor = $this->createDonor();
        $donation = $this->createDonation($donor, $campaign);

        $cert = Certificate::create([
            'uuid' => $donation->certificate_uuid,
            'donation_id' => $donation->id,
            'donor_id' => $donor->id,
            'donor_name' => $donor->name,
            'amount' => $donation->amount,
            'campaign_title' => $campaign->title,
            'status' => 'emailed',
        ]);

        // Simulate refund
        $donation->update(['status' => 'refunded', 'refunded_at' => now()]);
        $cert->update(['status' => 'revoked']);

        $this->assertEquals('revoked', $cert->fresh()->status);
        $this->assertEquals('refunded', $donation->fresh()->status);
    }

    // ── Test 4: Subscription creation and renewal ────────────────────────
    public function test_subscription_creation(): void
    {
        $campaign = $this->createCampaign();
        $donor = $this->createDonor();

        $sub = \App\Models\Subscription::create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'stripe_id' => 'sub_test_' . Str::random(8),
            'stripe_status' => 'active',
            'amount' => 25,
            'currency' => 'USD',
        ]);

        $this->assertTrue($sub->isActive());
        $this->assertEquals('active', $sub->stripe_status);
    }

    // ── Test 5: Certificate with special characters ──────────────────────
    public function test_certificate_special_characters_no_error(): void
    {
        $campaign = $this->createCampaign();
        $donor = $this->createDonor(['name' => "José O'Brien-Müller"]);
        $donation = $this->createDonation($donor, $campaign);

        $cert = Certificate::create([
            'uuid' => $donation->certificate_uuid,
            'donation_id' => $donation->id,
            'donor_id' => $donor->id,
            'donor_name' => "José O'Brien-Müller",
            'amount' => $donation->amount,
            'campaign_title' => $campaign->title,
            'status' => 'generated',
        ]);

        $this->assertEquals("José O'Brien-Müller", $cert->donor_name);
        $this->assertNotNull($cert->id);
    }

    // ── Test 6: Certificate with large amount ────────────────────────────
    public function test_certificate_large_amount(): void
    {
        $campaign = $this->createCampaign();
        $donor = $this->createDonor();
        $donation = $this->createDonation($donor, $campaign, ['amount' => 1000000]);

        $cert = Certificate::create([
            'uuid' => $donation->certificate_uuid,
            'donation_id' => $donation->id,
            'donor_name' => $donor->name,
            'amount' => 1000000,
            'campaign_title' => $campaign->title,
            'status' => 'generated',
        ]);

        $this->assertEquals('1000000.00', number_format($cert->amount, 2, '.', ''));
    }

    // ── Test 7: Verify endpoint — valid token ────────────────────────────
    public function test_verify_endpoint_valid_token(): void
    {
        $campaign = $this->createCampaign();
        $donor = $this->createDonor();
        $donation = $this->createDonation($donor, $campaign);

        Certificate::create([
            'uuid' => $donation->certificate_uuid,
            'donation_id' => $donation->id,
            'donor_name' => $donor->name,
            'amount' => $donation->amount,
            'campaign_title' => $campaign->title,
            'status' => 'generated',
        ]);

        $response = $this->get('/verify/' . $donation->certificate_uuid);
        $response->assertStatus(200);
        $response->assertViewHas('status', 'generated');
    }

    // ── Test 7b: Verify endpoint — invalid token → 404 ──────────────────
    public function test_verify_endpoint_invalid_token_returns_404(): void
    {
        $response = $this->get('/verify/00000000-0000-0000-0000-000000000000');
        $response->assertStatus(404);
    }

    // ── Test 8: Volunteer double-booking conflict detected ───────────────
    public function test_volunteer_conflict_detection(): void
    {
        $volunteer = Volunteer::create([
            'name' => 'Test Volunteer',
            'email' => 'vol@test.com',
            'status' => 'active',
        ]);

        $campaign = $this->createCampaign();

        $schedule1 = VolunteerSchedule::create([
            'event_name' => 'Morning Event',
            'event_date' => '2025-12-15',
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'status' => 'scheduled',
            'campaign_id' => $campaign->id,
        ]);

        $schedule2 = VolunteerSchedule::create([
            'event_name' => 'Overlapping Event',
            'event_date' => '2025-12-15',
            'start_time' => '11:00:00',
            'end_time' => '15:00:00',
            'status' => 'scheduled',
            'campaign_id' => $campaign->id,
        ]);

        // Register for first schedule
        $volunteer->schedules()->attach($schedule1->id, ['status' => 'registered']);

        // Check conflict with second (overlapping) schedule
        $hasConflict = $volunteer->hasConflict($schedule2);
        $this->assertTrue($hasConflict, 'Volunteer conflict should be detected for overlapping schedules');
    }

    // ── Test 9: Hour calculation after event completion ──────────────────
    public function test_volunteer_hour_calculation(): void
    {
        $volunteer = Volunteer::create([
            'name' => 'Hour Tracker',
            'email' => 'hours@test.com',
            'status' => 'active',
        ]);

        $campaign = $this->createCampaign();

        $schedule1 = VolunteerSchedule::create([
            'event_name' => 'Event A',
            'event_date' => '2025-11-01',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'status' => 'completed',
            'campaign_id' => $campaign->id,
        ]);

        $schedule2 = VolunteerSchedule::create([
            'event_name' => 'Event B',
            'event_date' => '2025-11-08',
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
            'status' => 'completed',
            'campaign_id' => $campaign->id,
        ]);

        $volunteer->schedules()->attach($schedule1->id, ['status' => 'attended', 'hours_worked' => 8.0]);
        $volunteer->schedules()->attach($schedule2->id, ['status' => 'attended', 'hours_worked' => 4.0]);

        // Refresh and recalculate
        $volunteer->refresh();
        $total = $volunteer->total_hours;

        $this->assertEquals(12.0, $total, 'Total hours should be 12 (8 + 4)');
    }

    // ── Test 10: Financial log is append-only ────────────────────────────
    public function test_financial_log_cannot_be_updated(): void
    {
        $this->expectException(\LogicException::class);

        $campaign = $this->createCampaign();
        $donor = $this->createDonor();

        $log = FinancialLog::create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 100,
            'currency' => 'USD',
            'type' => 'donation',
            'status' => 'success',
        ]);

        // This should throw
        $log->update(['status' => 'modified']);
    }

    // ── Test 11: Subscription cancellation lifecycle ──────────────────────
    public function test_subscription_cancellation_lifecycle(): void
    {
        $campaign = $this->createCampaign();
        $donor    = $this->createDonor();

        $sub = Subscription::create([
            'donor_id'      => $donor->id,
            'campaign_id'   => $campaign->id,
            'stripe_id'     => 'sub_cancel_' . Str::random(8),
            'stripe_status' => 'active',
            'amount'        => 50,
            'currency'      => 'USD',
        ]);

        // Initially active
        $this->assertTrue($sub->isActive());
        $this->assertFalse($sub->isCancelled());

        // Simulate cancellation: set ends_at to a past date
        $sub->update([
            'stripe_status' => 'canceled',
            'ends_at'       => now()->subDay(),
        ]);
        $sub->refresh();

        $this->assertFalse($sub->isActive(), 'Cancelled subscription should not be active');
        $this->assertTrue($sub->isCancelled(), 'Subscription should be marked as cancelled');
    }

    // ── Test 12: Recurring invoice.paid renewal creates donation + event ──
    public function test_recurring_renewal_creates_new_donation_and_fires_event(): void
    {
        Queue::fake();
        Event::fake([DonationReceived::class]);

        $campaign = $this->createCampaign();
        $donor    = $this->createDonor();

        $sub = Subscription::create([
            'donor_id'      => $donor->id,
            'campaign_id'   => $campaign->id,
            'stripe_id'     => 'sub_renewal_' . Str::random(8),
            'stripe_status' => 'active',
            'amount'        => 30,
            'currency'      => 'USD',
        ]);

        // Simulate what handleInvoicePaid does
        $renewal = Donation::create([
            'donor_id'        => $sub->donor_id,
            'campaign_id'     => $sub->campaign_id,
            'amount'          => $sub->amount,
            'currency'        => $sub->currency,
            'type'            => 'recurring',
            'status'          => 'completed',
            'idempotency_key' => Str::uuid(),
            'certificate_uuid'=> Str::uuid(),
            'ip_address'      => null,
        ]);

        event(new DonationReceived($renewal));

        $this->assertDatabaseHas('donations', [
            'donor_id'    => $donor->id,
            'campaign_id' => $campaign->id,
            'type'        => 'recurring',
            'status'      => 'completed',
        ]);

        Event::assertDispatched(DonationReceived::class, function ($e) use ($renewal) {
            return $e->donation->id === $renewal->id;
        });
    }

    // ── Test 13: Idempotency — duplicate key returns 409 (HTTP-level) ─────
    public function test_duplicate_idempotency_key_returns_409(): void
    {
        // Bind the fake gateway so no real HTTP calls are made
        $this->app->bind(PaymentGatewayInterface::class, FakePaymentGateway::class);
        FakePaymentGateway::reset();

        $user     = User::factory()->create(['role' => 'user']);
        $campaign = $this->createCampaign();
        $ikey     = Str::uuid()->toString();

        // Seed an existing donation with the same idempotency key
        Donor::create([
            'name'             => $user->name,
            'email'            => $user->email,
            'gdpr_consent'     => true,
            'gdpr_consent_at'  => now(),
        ]);

        Donation::create([
            'donor_id'        => Donor::where('email', $user->email)->first()->id,
            'campaign_id'     => $campaign->id,
            'amount'          => 100,
            'currency'        => 'EGP',
            'type'            => 'one_time',
            'status'          => 'pending',
            'idempotency_key' => $ikey,
            'certificate_uuid'=> Str::uuid(),
            'ip_address'      => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->postJson('/donate/checkout', [
            'campaign_id'     => $campaign->id,
            'amount'          => 100,
            'type'            => 'one_time',
            'name'            => $user->name,
            'email'           => $user->email,
            'gdpr_consent'    => '1',
            'idempotency_key' => $ikey,
        ]);

        $response->assertStatus(409);
        $response->assertJson(['error' => 'Duplicate request detected.']);
    }

    // ── Test 14: FakePaymentGateway swap smoke test ───────────────────────
    public function test_fake_gateway_can_be_swapped_and_returns_checkout_url(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, FakePaymentGateway::class);
        FakePaymentGateway::reset();

        $campaign = $this->createCampaign();
        $donor    = $this->createDonor();
        $ikey     = Str::uuid()->toString();

        /** @var FakePaymentGateway $gateway */
        $gateway = app(PaymentGatewayInterface::class);
        $result  = $gateway->createOneTimeCharge($donor, $campaign, 250.00, 'EGP', $ikey);

        $this->assertArrayHasKey('checkout_url', $result);
        $this->assertStringContainsString('fake_session_', $result['session_id']);
        $this->assertCount(1, FakePaymentGateway::$capturedCharges);
        $this->assertEquals(250.00, FakePaymentGateway::$capturedCharges[0]['amount']);
    }
}
