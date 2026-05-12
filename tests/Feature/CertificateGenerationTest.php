<?php

namespace Tests\Feature;

use App\Jobs\CertificateGenerationJob;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CertificateGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        // Ensure the local storage is also faked if the job uses default disk
        Storage::fake();
    }

    public function test_certificate_generation_with_special_characters(): void
    {
        $campaign = Campaign::factory()->create(['title' => 'Special Characters Test & < > " \'']);
        $donor = Donor::factory()->create(['name' => 'Jérôme O\'Neill-Müller & 龍']);
        $donation = Donation::factory()->create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 100.00,
            'certificate_uuid' => (string) Str::uuid(),
        ]);

        CertificateGenerationJob::dispatchSync($donation);

        $filename = 'certificates/' . $donation->certificate_uuid . '.pdf';
        Storage::assertExists($filename);
        $this->assertGreaterThan(0, strlen(Storage::get($filename)));
        
        $this->assertDatabaseHas('certificates', [
            'donation_id' => $donation->id,
            'donor_name' => 'Jérôme O\'Neill-Müller & 龍',
        ]);
    }

    public function test_certificate_generation_with_large_amount(): void
    {
        $campaign = Campaign::factory()->create();
        $donor = Donor::factory()->create();
        $donation = Donation::factory()->create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 999999999.99,
            'certificate_uuid' => (string) Str::uuid(),
        ]);

        CertificateGenerationJob::dispatchSync($donation);

        $filename = 'certificates/' . $donation->certificate_uuid . '.pdf';
        Storage::assertExists($filename);
        $this->assertGreaterThan(0, strlen(Storage::get($filename)));

        $this->assertDatabaseHas('certificates', [
            'donation_id' => $donation->id,
            'amount' => 999999999.99,
        ]);
    }

    public function test_certificate_generation_with_long_titles(): void
    {
        $longTitle = str_repeat('Very Long Campaign Title ', 10);
        $campaign = Campaign::factory()->create(['title' => $longTitle]);
        $donor = Donor::factory()->create(['name' => str_repeat('Long Name ', 10)]);
        $donation = Donation::factory()->create([
            'donor_id' => $donor->id,
            'campaign_id' => $campaign->id,
            'amount' => 1234.56,
            'certificate_uuid' => (string) Str::uuid(),
        ]);

        CertificateGenerationJob::dispatchSync($donation);

        $filename = 'certificates/' . $donation->certificate_uuid . '.pdf';
        Storage::assertExists($filename);
        $this->assertGreaterThan(0, strlen(Storage::get($filename)));
    }
}
