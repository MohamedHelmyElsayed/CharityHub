<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\VolunteerEvent;
use App\Models\VolunteerShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VolunteerSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create an admin user to own events
        $admin = User::where('role', 'admin')->first()
            ?? User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping seeder.');
            return;
        }

        // Find a campaign to link events to
        $campaign = Campaign::first();

        // Create volunteer events
        $eventsData = [
            [
                'title'       => 'Community Food Drive 2026',
                'event_type'  => 'food_drive',
                'description' => 'Help us collect and distribute food to families in need across the city.',
                'location'    => 'Main Community Center, Cairo',
                'start_date'  => now()->addDays(7),
                'end_date'    => now()->addDays(7)->addHours(8),
                'max_volunteers' => 20,
                'status'      => 'open',
                'shifts'      => [
                    ['title' => 'Morning Shift', 'start_time' => '08:00', 'end_time' => '12:00', 'required_volunteers' => 10],
                    ['title' => 'Afternoon Shift', 'start_time' => '13:00', 'end_time' => '17:00', 'required_volunteers' => 10],
                ],
            ],
            [
                'title'       => 'Beach Cleanup Initiative',
                'event_type'  => 'cleanup',
                'description' => 'Join us for a day of environmental action cleaning Alexandria beaches.',
                'location'    => 'Alexandria Corniche',
                'start_date'  => now()->addDays(14),
                'end_date'    => now()->addDays(14)->addHours(5),
                'max_volunteers' => 30,
                'status'      => 'open',
                'shifts'      => [
                    ['title' => 'Early Bird Shift', 'start_time' => '07:00', 'end_time' => '10:00', 'required_volunteers' => 15],
                    ['title' => 'Main Shift', 'start_time' => '10:00', 'end_time' => '13:00', 'required_volunteers' => 15],
                ],
            ],
            [
                'title'       => 'Youth Education Workshop',
                'event_type'  => 'education',
                'description' => 'Tutoring and mentoring sessions for underprivileged youth.',
                'location'    => 'Heliopolis Library',
                'start_date'  => now()->addDays(21),
                'end_date'    => now()->addDays(21)->addHours(6),
                'max_volunteers' => 12,
                'status'      => 'open',
                'shifts'      => [
                    ['title' => 'Math & Science', 'start_time' => '09:00', 'end_time' => '12:00', 'required_volunteers' => 6],
                    ['title' => 'Language Arts', 'start_time' => '13:00', 'end_time' => '16:00', 'required_volunteers' => 6],
                ],
            ],
        ];

        foreach ($eventsData as $eventData) {
            $shiftsData = $eventData['shifts'];
            unset($eventData['shifts']);

            $event = VolunteerEvent::create(array_merge($eventData, [
                'created_by'  => $admin->id,
                'campaign_id' => $campaign?->id,
                'slug'        => Str::slug($eventData['title']) . '-' . now()->format('Y'),
                'registration_deadline' => $eventData['start_date']->copy()->subDays(2),
                'required_skills' => ['teamwork', 'communication'],
            ]));

            foreach ($shiftsData as $shiftData) {
                VolunteerShift::create(array_merge($shiftData, [
                    'event_id'   => $event->id,
                    'shift_date' => $event->start_date->toDateString(),
                    'description' => 'Please arrive 10 minutes early for briefing.',
                    'location'   => $event->location,
                    'status'     => 'open',
                ]));
            }
        }

        $this->command->info('✅ Volunteer system seeded: 3 events, 6 shifts created.');
    }
}
