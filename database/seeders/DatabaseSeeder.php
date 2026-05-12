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
            'title'             => 'Clean Water for East Africa',
            'slug'              => 'clean-water-africa',
            'short_description' => 'Provide safe, clean drinking water to 10,000 families in Kenya and Tanzania.',
            'description'       => "Every year, millions of people in East Africa face severe water scarcity. Our campaign aims to build 50 solar-powered water wells across rural Kenya and Tanzania, providing safe drinking water to over 10,000 families.\n\nYour donation funds well construction, water quality testing equipment, and local maintenance training to ensure sustainability for decades to come.",
            'goal_amount'       => 75000,
            'current_amount'    => 31450,
            'deadline'          => now()->addDays(45),
            'status'            => 'active',
            'featured'          => true,
            'cover_image'       => 'campaigns/clean-water.jpg',
        ]);

        $campaign2 = Campaign::firstOrCreate(['slug' => 'education-girls-stem'], [
            'title'             => 'Girls in STEM Scholarships',
            'slug'              => 'education-girls-stem',
            'short_description' => 'Fund university STEM scholarships for 100 talented girls from underserved communities.',
            'description'       => "Education is the most powerful tool for change. We are funding full university scholarships for 100 brilliant girls who lack the financial resources to pursue their dreams in Science, Technology, Engineering, and Mathematics.\n\nEach \$1,500 scholarship covers tuition, textbooks, and a laptop for one full academic year.",
            'goal_amount'       => 150000,
            'current_amount'    => 89200,
            'deadline'          => now()->addDays(90),
            'status'            => 'active',
            'featured'          => true,
            'cover_image'       => 'campaigns/girls-stem.jpg',
        ]);

        $campaign3 = Campaign::firstOrCreate(['slug' => 'food-security-2025'], [
            'title'             => 'Food Security Initiative 2025',
            'slug'              => 'food-security-2025',
            'short_description' => 'Feed 5,000 families during the agricultural off-season.',
            'description'       => "Climate change has disrupted traditional farming seasons across sub-Saharan Africa. Our Food Security Initiative provides emergency food packages to 5,000 families during the 3-month agricultural off-season.\n\n\$50 feeds a family of 5 for one full month.",
            'goal_amount'       => 50000,
            'current_amount'    => 12800,
            'deadline'          => now()->addDays(30),
            'status'            => 'active',
            'cover_image'       => 'campaigns/food-security.jpg',
        ]);

        $campaign4 = Campaign::firstOrCreate(['slug' => 'emergency-medical-relief'], [
            'title'             => 'Emergency Medical Relief Fund',
            'slug'              => 'emergency-medical-relief',
            'short_description' => 'Provide critical medical supplies and field clinics to conflict-affected communities.',
            'description'       => "Thousands of families in conflict zones have no access to basic medical care. This fund deploys mobile field clinics and distributes essential supplies — medicines, surgical kits, and maternal care packages — to areas cut off from hospitals.\n\nEvery \$100 provides emergency medical care for one family for a month. Your donation can save lives today.",

            'goal_amount'       => 100000,
            'current_amount'    => 41750,
            'deadline'          => now()->addDays(60),
            'status'            => 'active',
            'featured'          => true,
            'cover_image'       => 'campaigns/medical-relief.jpg',
        ]);

        $campaign5 = Campaign::firstOrCreate(['slug' => 'reforestation-2025'], [
            'title'             => 'Reforestation Initiative 2025',
            'slug'              => 'reforestation-2025',
            'short_description' => 'Plant 1 million trees across degraded land to fight climate change.',
            'description'       => "Deforestation has stripped millions of hectares of land bare, accelerating climate change and destroying ecosystems. Our Reforestation Initiative partners with local communities to plant one million trees across five countries.\n\nFor just \$5 you can plant a tree that will absorb CO₂ for decades. Every tree planted is tracked via GPS and reported back to donors.",
            'goal_amount'       => 50000,
            'current_amount'    => 8300,
            'deadline'          => now()->addDays(120),
            'status'            => 'active',
            'cover_image'       => 'campaigns/reforestation.jpg',
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

        // ── Subscriptions ─────────────────────────────────────────────────────
        // Active monthly subscription (donor1 → campaign2)
        Subscription::firstOrCreate(
            ['gateway_subscription_id' => 'sub_seed_active_001'],
            [
                'donor_id'                => $donor1->id,
                'campaign_id'             => $campaign2->id,
                'gateway_subscription_id' => 'sub_seed_active_001',
                'status'                  => 'active',
                'amount'                  => 25,
                'currency'                => 'USD',
            ]
        );

        // Cancelled subscription (donor2 → campaign1)
        Subscription::firstOrCreate(
            ['gateway_subscription_id' => 'sub_seed_cancelled_002'],
            [
                'donor_id'                => $donor2->id,
                'campaign_id'             => $campaign1->id,
                'gateway_subscription_id' => 'sub_seed_cancelled_002',
                'status'                  => 'canceled',
                'amount'                  => 50,
                'currency'                => 'USD',
                'ends_at'                 => now()->subDays(15),
            ]
        );

        // ── Financial Logs ────────────────────────────────────────────────────
        FinancialLog::create([
            'donor_id'               => $donor1->id,
            'campaign_id'            => $campaign1->id,
            'donation_id'            => $donation1->id,
            'amount'                 => 500,
            'currency'               => 'USD',
            'transaction_type'       => 'donation',
            'gateway_transaction_id' => 'evt_seed_001',
            'status'                 => 'success',
            'metadata'               => ['donation_type' => 'one_time', 'campaign_title' => $campaign1->title],
            'ip_address'             => '127.0.0.1',
        ]);

        FinancialLog::create([
            'donor_id'               => $donor2->id,
            'campaign_id'            => $campaign2->id,
            'donation_id'            => $donation2->id,
            'amount'                 => 1500,
            'currency'               => 'USD',
            'transaction_type'       => 'donation',
            'gateway_transaction_id' => 'evt_seed_002',
            'status'                 => 'success',
            'metadata'               => ['donation_type' => 'one_time', 'campaign_title' => $campaign2->title],
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

        // Additional schedules — one per remaining campaign (shown in volunteer sign-up)
        VolunteerSchedule::firstOrCreate(['event_name' => 'Food Package Distribution Day'], [
            'event_name'     => 'Food Package Distribution Day',
            'description'    => 'Help pack and distribute emergency food packages to affected families.',
            'location'       => 'Nairobi Distribution Hub — Gate C',
            'event_date'     => now()->addDays(10)->toDateString(),
            'start_time'     => '07:00:00',
            'end_time'       => '15:00:00',
            'max_volunteers' => 25,
            'status'         => 'scheduled',
            'campaign_id'    => $campaign3->id,
        ]);

        VolunteerSchedule::firstOrCreate(['event_name' => 'Mobile Clinic Setup & Patient Support'], [
            'event_name'     => 'Mobile Clinic Setup & Patient Support',
            'description'    => 'Assist medical teams with clinic setup, patient registration, and supply management.',
            'location'       => 'Mombasa — Field Hospital Site B',
            'event_date'     => now()->addDays(18)->toDateString(),
            'start_time'     => '06:00:00',
            'end_time'       => '18:00:00',
            'max_volunteers' => 15,
            'status'         => 'scheduled',
            'campaign_id'    => $campaign4->id,
        ]);

        VolunteerSchedule::firstOrCreate(['event_name' => 'Tree Planting Marathon — Rift Valley'], [
            'event_name'     => 'Tree Planting Marathon — Rift Valley',
            'description'    => 'Plant native tree species to restore the Rift Valley forest belt.',
            'location'       => 'Nakuru, Rift Valley — Reforestation Site',
            'event_date'     => now()->addDays(21)->toDateString(),
            'start_time'     => '07:30:00',
            'end_time'       => '17:00:00',
            'max_volunteers' => 50,
            'status'         => 'scheduled',
            'campaign_id'    => $campaign5->id,
        ]);

        VolunteerSchedule::firstOrCreate(['event_name' => 'Fundraising Gala — Event Coordination'], [
            'event_name'     => 'Fundraising Gala — Event Coordination',
            'description'    => 'Volunteer coordinators needed for CharityHub annual gala dinner.',
            'location'       => 'Nairobi Serena Hotel — Grand Ballroom',
            'event_date'     => now()->addDays(35)->toDateString(),
            'start_time'     => '14:00:00',
            'end_time'       => '23:00:00',
            'max_volunteers' => 12,
            'status'         => 'scheduled',
            'campaign_id'    => $campaign1->id,
        ]);

        VolunteerSchedule::firstOrCreate(['event_name' => 'After-School Tutoring Program Launch'], [
            'event_name'     => 'After-School Tutoring Program Launch',
            'description'    => 'Support the launch of our after-school tutoring centres for scholarship girls.',
            'location'       => 'Kampala, Uganda — Scholarship Centre',
            'event_date'     => now()->addDays(42)->toDateString(),
            'start_time'     => '13:00:00',
            'end_time'       => '18:00:00',
            'max_volunteers' => 20,
            'status'         => 'scheduled',
            'campaign_id'    => $campaign2->id,
        ]);

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

        // Impact photos
        \App\Models\ImpactPhoto::firstOrCreate(['impact_report_id' => $report1->id, 'path' => 'impacts/water-wells.jpg'], [
            'impact_report_id' => $report1->id,
            'path'             => 'impacts/water-wells.jpg',
            'caption'          => 'Solar-powered water well serving Makueni Village, Kenya',
            'sort_order'       => 0,
        ]);

        // ── Impact Report 2: Girls in STEM ────────────────────────────────

        $report2 = ImpactReport::firstOrCreate(['title' => 'Girls in STEM — Mid-Year Scholarship Update'], [
            'campaign_id'        => $campaign2->id,
            'title'              => 'Girls in STEM — Mid-Year Scholarship Update',
            'slug'               => 'girls-in-stem-mid-year-scholarship-update',
            'outcomes_narrative' => "As of June 2025, the Girls in STEM Scholarship Fund has awarded 62 full university scholarships to talented young women from underserved communities across 8 countries.\n\nKey outcomes:\n• 62 scholarships awarded (target: 100 by year-end)\n• Average GPA of recipients: 3.7/4.0\n• 14 recipient internship placements secured at top tech firms\n• 3 recipients have already published academic research papers\n\nOne recipient, Amara from Lagos, wrote: 'This scholarship didn't just fund my degree — it gave me my future.' We are on track to fund all 100 scholarships by December 2025.",
            'beneficiary_count'  => 62,
            'funds_used'         => 93000,
            'report_period'      => 'H1 2025 (January – June)',
            'status'             => 'published',
        ]);

        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report2->id, 'name' => 'University of Lagos'], [
            'impact_report_id' => $report2->id,
            'name'             => 'University of Lagos',
            'latitude'         => 6.5244,
            'longitude'        => 3.3792,
            'description'      => '18 scholarships — Nigeria',
            'beneficiaries'    => 18,
        ]);
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report2->id, 'name' => 'Kenyatta University'], [
            'impact_report_id' => $report2->id,
            'name'             => 'Kenyatta University',
            'latitude'         => -1.2921,
            'longitude'        => 36.8219,
            'description'      => '22 scholarships — Kenya',
            'beneficiaries'    => 22,
        ]);
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report2->id, 'name' => 'University of Ghana'], [
            'impact_report_id' => $report2->id,
            'name'             => 'University of Ghana',
            'latitude'         => 5.6502,
            'longitude'        => -0.1862,
            'description'      => '22 scholarships — Ghana',
            'beneficiaries'    => 22,
        ]);

        \App\Models\ImpactPhoto::firstOrCreate(['impact_report_id' => $report2->id, 'path' => 'impacts/stem-graduates.jpg'], [
            'impact_report_id' => $report2->id,
            'path'             => 'impacts/stem-graduates.jpg',
            'caption'          => 'Scholarship recipients celebrating graduation at Kenyatta University',
            'sort_order'       => 0,
        ]);

        // ── Impact Report 3: Food Security ───────────────────────────────

        $report3 = ImpactReport::firstOrCreate(['title' => 'Food Security Initiative — Emergency Response Report'], [
            'campaign_id'        => $campaign3->id,
            'title'              => 'Food Security Initiative — Emergency Response Report',
            'slug'               => 'food-security-initiative-emergency-response-report',
            'outcomes_narrative' => "An unprecedented drought in Q1 2025 threatened food security for over 20,000 people across three districts. CharityHub activated our emergency food distribution network within 72 hours of the crisis declaration.\n\nKey outcomes:\n• 3,200 emergency food packages distributed within 2 weeks\n• 16,000 people received immediate food assistance\n• 45 local distribution partners mobilised\n• Average delivery time: 18 hours from warehouse to recipient\n\n\$50 fed a family of five for one full month. This rapid response prevented a projected 34% increase in acute malnutrition rates in the affected areas.",
            'beneficiary_count'  => 16000,
            'funds_used'         => 12800,
            'report_period'      => 'Emergency Response — March 2025',
            'status'             => 'published',
        ]);

        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report3->id, 'name' => 'Turkana County'], [
            'impact_report_id' => $report3->id,
            'name'             => 'Turkana County',
            'latitude'         => 3.1166,
            'longitude'        => 35.5966,
            'description'      => '1,400 packages — 7,000 people',
            'beneficiaries'    => 7000,
        ]);
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report3->id, 'name' => 'Marsabit County'], [
            'impact_report_id' => $report3->id,
            'name'             => 'Marsabit County',
            'latitude'         => 2.3284,
            'longitude'        => 37.9897,
            'description'      => '1,100 packages — 5,500 people',
            'beneficiaries'    => 5500,
        ]);
        BeneficiaryLocation::firstOrCreate(['impact_report_id' => $report3->id, 'name' => 'Wajir County'], [
            'impact_report_id' => $report3->id,
            'name'             => 'Wajir County',
            'latitude'         => 1.7471,
            'longitude'        => 40.0573,
            'description'      => '700 packages — 3,500 people',
            'beneficiaries'    => 3500,
        ]);



        $this->command->info('✅ CharityHub demo data seeded successfully!');
        $this->command->info('   Admin: admin@charityhub.org / password');
        $this->command->info('   Staff: staff@charityhub.org / password');
        $this->command->info('   Donor: alice@example.com / password');
        $this->command->info('   Certificate verify URL: /verify/' . $uuid1);
    }
}
