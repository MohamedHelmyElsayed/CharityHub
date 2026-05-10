<?php

namespace App\Listeners;

use App\Events\VolunteerApproved;
use App\Notifications\VolunteerApprovedNotification;

class SendVolunteerApprovalNotification
{
    public function handle(VolunteerApproved $event): void
    {
        $volunteer = $event->volunteer;
        $user = $volunteer->user;
        if ($user) {
            $user->notify(new VolunteerApprovedNotification($volunteer));
        }
    }
}
