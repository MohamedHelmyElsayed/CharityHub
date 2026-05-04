<?php

namespace Database\Seeders;

use App\Models\BeneficiaryLocation;
use App\Models\Campaign;
use App\Models\Certificate;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\FinancialLog;
use App\Models\ImpactPhoto;
use App\Models\ImpactReport;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\VolunteerSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@charityhub.org'], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $employee = User::firstOrCreate(['email' => 'staff@charityhub.org'], [
            'name' => 'Staff Member',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        $donor1User = User::firstOrCreate(['email' => 'alice@example.com'], [
            'name' => 'Alice Johnson',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
        $donor2User = User::firstOrCreate(['email' => 'bob@example.com'], [
            'name' => 'Bob Smith',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // ── Donors ─────────────────────────────────────────────────────────
        $donor1 = Donor::firstOrCreate(['email' => 'alice@example.com'], [
            'user_id' => $donor1User->id,
            'name' => 'Alice Johnson',
            'phone' => '+1-555-0101',
            'anonymous' => false,
            'gdpr_consent' => true,
            'gdpr_consent_at' => now(),
            'marketing_opt_in' => true,
        ]);
        $donor2 = Donor::firstOrCreate(['email' => 'bob@example.com'], [
            'user_id' => $donor2User->id,
            'name' => 'Bob Smith',
            'phone' => '+1-555-0102',
            'anonymous' => false,
            'gdpr_consent' => true,
            'gdpr_consent_at' => now(),
        ]);
        $donor3 = Donor::firstOrCreate(['email' => 'jose.obrien@example.com'], [
            'name' => "José O'Brien-Müller",
            'phone' => '+49-555-0103',
            'anonymous' => false,
            'gdpr_consent' => true,
            'gdpr_consent_at' => now(),
        ]);

        // ── Campaigns ─────────────────────────────────────────────────────
        $campaign1 = Campaign::firstOrCreate(['slug' => 'clean-water-africa'], [
            'title' => 'Clean Water for East Africa',
            'slug' => 'clean-water-africa',
            'short_description' => 'Provide safe, clean drinking water to 10,000 families in Kenya and Tanzania.',
            'description' => "Every year, millions of people in East Africa face severe water scarcity. Our campaign aims to build 50 solar-powered water wells across rural Kenya and Tanzania, providing safe drinking water to over 10,000 families.\n\nYour donation funds well construction, water quality testing equipment, and local maintenance training to ensure sustainability for decades to come.",
            'goal_amount' => 75000,
            'current_amount' => 31450,
            'deadline' => now()->addDays(45),
            'status' => 'active',
            'featured' => true,
        ]);

        $campaign2 = Campaign::firstOrCreate(['slug' => 'education-girls-stem'], [
            'title' => 'Girls in STEM Scholarships',
            'slug' => 'education-girls-stem',
            'short_description' => 'Fund university STEM scholarships for 100 talented girls from underserved communities.',
            'description' => "Education is the most powerful tool for change. We are funding full university scholarships for 100 brilliant girls who lack the financial resources to pursue their dreams in Science, Technology, Engineering, and Mathematics.\n\nEach $1,500 scholarship covers tuition, textbooks, and a laptop for one full academic year.",
            'goal_amount' => 150000,
            'current_amount' => 89200,
            'deadline' => now()->addDays(90),
            'status' => 'active',
            'featured' => true,
        ]);

        $campaign3 = Campaign::firstOrCreate(['slug' => 'food-security-2025'], [
            'title' => 'Food Security Initiative 2025',
            'slug' => 'food-security-2025',
            'short_description' => 'Feed 5,000 families during the agricultural off-season.',
            'description' => "Climate change has disrupted traditional farming seasons across sub-Saharan Africa. Our Food Security Initiative provides emergency food packages to 5,000 families during the 3-month agricultural off-season.\n\n$50 feeds a family of 5 for one full month.",
            'goal_amount' => 50000,
            'current_amount' => 12800,
            'deadline' => now()->addDays(30),
            'status' => 'active',
        ]);

        // ── Donations ─────────────────────────────────────────────────────
        $uuid1 = Str::uuid()->toString();
        $donation1 = Donation::firstOrCreate(['idempotency_key' => 'seed-don-001'], [
            'user_id' => $donor1User->id,
            'donor_id' => $donor1->id,
            'campaign_id' => $campaign1->id,
            'amount' => 500,
            'currency' => 'USD',
            'type' => 'one_time',
            'status' => 'completed',
            'idempotency_key' => 'seed-don-001',
            'certificate_uuid' => $uuid1,
            'anonymous' => false,
            'message' => 'This cause is close to my heart. Keep up the great work!',
            'ip_address' => '127.0.0.1',
        ]);

        $uuid2 = Str::uuid()->toString();
        $donation2 = Donation::firstOrCreate(['idempotency_key' => 'seed-don-002'], [
            'user_id' => $donor2User->id,
            'donor_id' => $donor2->id,
            'campaign_id' => $campaign2->id,
            'amount' => 1500,
            'currency' => 'USD',
            'type' => 'one_time',
            'status' => 'completed',
            'idempotency_key' => 'seed-don-002',
            'certificate_uuid' => $uuid2,
            'anonymous' => false,
        ]);

        $uuid3 = Str::uuid()->toString();
        $donation3 = Donation::firstOrCreate(['idempotency_key' => 'seed-don-003-special'], [
            'donor_id' => $donor3->id,
            'campaign_id' => $campaign1->id,
            'amount' => 1000000,
            'currency' => 'USD',
            'type' => 'one_time',
            'status' => 'completed',
            'idempotency_key' => 'seed-don-003-special',
            'certificate_uuid' => $uuid3,
            'anonymous' => false,
        ]);

        // ── Certificates ──────────────────────────────────────────────────
        Certificate::firstOrCreate(['donation_id' => $donation1->id], [
            'uuid' => $uuid1,
            'donor_id' => $donor1->id,
            'donor_name' => 'Alice Johnson',
            'amount' => 500,
            'campaign_title' => $campaign1->title,
            'status' => 'emailed',
        ]);

        Certificate::firstOrCreate(['donation_id' => $donation2->id], [
            'uuid' => $uuid2,
            'donor_id' => $donor2->id,
            'donor_name' => 'Bob Smith',
            'amount' => 1500,
            'campaign_title' => $campaign2->title,
            'status' => 'generated',
        ]);

        Certificate::firstOrCreate(['donation_id' => $donation3->id], [
            'uuid' => $uuid3,
            'donor_id' => $donor3->id,
            'donor_name' => "José O'Brien-Müller",
            'amount' => 1000000,
            'campaign_title' => $campaign1->title,
            'status' => 'generated',
        ]);

        // ── Financial Logs ────────────────────────────────────────────────
        FinancialLog::create([
            'donor_id' => $donor1->id,
            'campaign_id' => $campaign1->id,
            'donation_id' => $donation1->id,
            'amount' => 500,
            'currency' => 'USD',
            'type' => 'donation',
            'stripe_event_id' => 'evt_seed_001',
            'status' => 'success',
            'metadata' => ['donation_type' => 'one_time', 'campaign_title' => $campaign1->title],
            'ip_address' => '127.0.0.1',
        ]);

        FinancialLog::create([
            'donor_id' => $donor2->id,
            'campaign_id' => $campaign2->id,
            'donation_id' => $donation2->id,
            'amount' => 1500,
            'currency' => 'USD',
            'type' => 'donation',
            'stripe_event_id' => 'evt_seed_002',
            'status' => 'success',
            'metadata' => ['donation_type' => 'one_time', 'campaign_title' => $campaign2->title],
        ]);

        // ── Volunteers ────────────────────────────────────────────────────
        $vol1 = Volunteer::firstOrCreate(['email' => 'sarah.volunteer@example.com'], [
            'user_id' => null,
            'name' => 'Sarah Chen',
            'email' => 'sarah.volunteer@example.com',
            'phone' => '+1-555-0201',
            'skills' => ['event coordination', 'social media', 'translation (Mandarin)'],
            'bio' => 'Passionate about humanitarian causes with 5 years of volunteer experience.',
            'status' => 'active',
        ]);

        $vol2 = Volunteer::firstOrCreate(['email' => 'mike.volunteer@example.com'], [
            'name' => 'Michael Torres',
            'email' => 'mike.volunteer@example.com',
            'phone' => '+1-555-0202',
            'skills' => ['logistics', 'driving', 'first aid'],
            'status' => 'active',
        ]);

        // ── Volunteer Schedules ───────────────────────────────────────────
        $sched1 = VolunteerSchedule::firstOrCreate(['event_name' => 'Community Water Well Inspection Day'], [
            'event_name' => 'Community Water Well Inspection Day',
            'description' => 'Monthly inspection and maintenance of installed water wells.',
            'location' => 'Nairobi, Kenya — Community Center',
            'event_date' => now()->addDays(7)->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'max_volunteers' => 10,
            'status' => 'scheduled',
            'campaign_id' => $campaign1->id,
        ]);

        $sched2 = VolunteerSchedule::firstOrCreate(['event_name' => 'STEM Mentorship Workshop'], [
            'event_name' => 'STEM Mentorship Workshop',
            'description' => 'Online mentorship sessions with scholarship recipients.',
            'location' => 'Virtual (Zoom)',
            'event_date' => now()->addDays(14)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
            'status' => 'scheduled',
            'campaign_id' => $campaign2->id,
        ]);

        // Assign volunteers to schedules
        $vol1->schedules()->syncWithoutDetaching([$sched1->id => ['status' => 'registered']]);
        $vol1->schedules()->syncWithoutDetaching([$sched2->id => ['status' => 'registered']]);
        $vol2->schedules()->syncWithoutDetaching([$sched1->id => ['status' => 'registered']]);

        // ── Impact Reports ────────────────────────────────────────────────
        $report1 = ImpactReport::firstOrCreate(['title' => 'Water Wells Q1 2025 Impact Report'], [
            'campaign_id' => $campaign1->id,
            'title' => 'Water Wells Q1 2025 Impact Report',
            'outcomes_narrative' => "In the first quarter of 2025, CharityHub completed the installation of 12 solar-powered water wells across rural Kenya, providing clean water access to approximately 2,800 families (14,000 people).\n\nKey outcomes:\n• 12 wells installed and fully operational\n• 14,000+ people with clean water access\n• 87% reduction in waterborne illness reports in served communities\n• 6 local technicians trained for ongoing maintenance\n\nChildren who previously spent 4+ hours daily fetching water can now attend school regularly. Women report spending this time on income-generating activities instead.",
            'beneficiary_count' => 14000,
            'funds_used' => 28500,
            'report_period' => 'Q1 2025 (January – March)',
            'status' => 'published',
        ]);

        // Beneficiary locations
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report1->id, 'name' => 'Makueni Village'], [
            'impact_report_id' => $report1->id,
            'name' => 'Makueni Village',
            'latitude' => -2.2667,
            'longitude' => 37.6167,
            'description' => '3 wells serving 3,200 people',
            'beneficiaries' => 3200,
        ]);
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report1->id, 'name' => 'Kajiado District'], [
            'impact_report_id' => $report1->id,
            'name' => 'Kajiado District',
            'latitude' => -1.8500,
            'longitude' => 36.7833,
            'description' => '5 wells serving 5,800 people',
            'beneficiaries' => 5800,
        ]);
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report1->id, 'name' => 'Taita Taveta'], [
            'impact_report_id' => $report1->id,
            'name' => 'Taita Taveta',
            'latitude' => -3.3167,
            'longitude' => 38.3500,
            'description' => '4 wells serving 5,000 people',
            'beneficiaries' => 5000,
        ]);

        $this->command->info('✅ CharityHub demo data seeded successfully!');
        $this->command->info('   Admin: admin@charityhub.org / password');
        $this->command->info('   Staff: staff@charityhub.org / password');
        $this->command->info('   Donor: alice@example.com / password');
        $this->command->info('   Certificate verify URL: /verify/' . $uuid1);
    }
}
