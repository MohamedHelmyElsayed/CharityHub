<?php

namespace Tests\Feature;

use App\Events\DonationReceived;
use App\Events\PaymentFailed;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\FinancialLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FinancialAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_received_event_creates_audit_log()
    {
        $campaign = Campaign::factory()->create();
        $donor = Donor::factory()->create();
        $donation = Donation::create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 100,
            'currency' => 'USD',
            'status' => 'completed',
            'idempotency_key' => Str::uuid()->toString(),
        ]);

        event(new DonationReceived($donation));

        $this->assertDatabaseHas('financial_logs', [
            'donation_id' => $donation->id,
            'transaction_type' => 'payment_success',
            'amount' => 100.00,
            'status' => 'success',
        ]);

        $log = FinancialLog::where('donation_id', $donation->id)->first();
    }

    public function test_payment_failed_event_creates_audit_log()
    {
        $campaign = Campaign::factory()->create();
        $donor = Donor::factory()->create();
        $donation = Donation::create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 50,
            'currency' => 'USD',
            'status' => 'failed',
            'idempotency_key' => Str::uuid()->toString(),
        ]);

        event(new PaymentFailed($donation, ['error' => 'Card declined', 'card_number' => '1234567890']));

        $this->assertDatabaseHas('financial_logs', [
            'donation_id' => $donation->id,
            'transaction_type' => 'payment_failed',
            'status' => 'failed',
        ]);

        $log = FinancialLog::where('donation_id', $donation->id)->first();
        $this->assertEquals('********', $log->metadata['card_number']);
    }

    public function test_financial_logs_are_immutable()
    {
        $log = FinancialLog::create([
            'transaction_type' => 'test',
            'amount' => 10,
            'status' => 'success',
        ]);

        $this->expectException(\LogicException::class);
        $log->update(['amount' => 20]);
    }

    public function test_financial_logs_cannot_be_deleted()
    {
        $log = FinancialLog::create([
            'transaction_type' => 'test',
            'amount' => 10,
            'status' => 'success',
        ]);

        $this->expectException(\LogicException::class);
        $log->delete();
    }

    public function test_idempotency_replays_successful_response()
    {
        $user = User::factory()->create();
        $key = Str::random(16);

        $payload = [
            'campaign_id' => Campaign::factory()->create()->id,
            'amount' => 100,
            'type' => 'one_time',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'gdpr_consent' => true,
            'idempotency_key' => $key,
        ];

        // First request
        $response1 = $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', $key)
            ->post('/donate/checkout', $payload);
        
        $response1->assertStatus(302);

        // Second request with same key and SAME data
        $response2 = $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', $key)
            ->post('/donate/checkout', $payload);

        // Should return 200 (replayed redirect info)
        $response2->assertStatus(200);
        $response2->assertHeader('X-Idempotency-Replay', 'true');
    }

    public function test_idempotency_blocks_collision()
    {
        $user = User::factory()->create();
        $key = Str::random(16);

        $payload1 = [
            'campaign_id' => Campaign::factory()->create()->id,
            'amount' => 100,
            'type' => 'one_time',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'gdpr_consent' => true,
            'idempotency_key' => $key,
        ];

        $payload2 = array_merge($payload1, ['amount' => 200]);

        // First request
        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', $key)
            ->post('/donate/checkout', $payload1)
            ->assertStatus(302);

        // Second request with same key but DIFFERENT data
        $response2 = $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', $key)
            ->post('/donate/checkout', $payload2);

        $response2->assertStatus(409);
        $this->assertDatabaseHas('financial_logs', [
            'transaction_type' => 'duplicate_request_blocked',
            'idempotency_key' => $key,
        ]);
    }
}
