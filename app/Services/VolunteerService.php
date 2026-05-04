<?php

namespace App\Services;

use App\Models\Volunteer;
use App\Models\VolunteerHour;
use App\Models\User;

class VolunteerService
{
    public function registerAsVolunteer(User $user)
    {
        $volunteer = Volunteer::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'status' => 'active',
        ]);

        $user->update(['subtype' => 'volunteer']);

        return $volunteer;
    }

    public function logHours($volunteerId, $date, $hours)
    {
        return VolunteerHour::create([
            'volunteer_id' => $volunteerId,
            'date' => $date,
            'hours' => $hours,
        ]);
    }

    public function getVolunteerStats($volunteerId)
    {
        $volunteer = Volunteer::with('hours')->findOrFail($volunteerId);
        return [
            'total_hours' => $volunteer->total_hours,
            'recent_activity' => $volunteer->hours()->latest()->take(5)->get(),
        ];
    }

    public function getAllVolunteers()
    {
        return Volunteer::with('user')->get();
    }
}
