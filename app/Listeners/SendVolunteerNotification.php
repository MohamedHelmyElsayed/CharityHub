<?php

namespace App\Listeners;

use App\Events\ShiftApproved;
use App\Notifications\ShiftApprovedNotification;
use App\Events\ShiftRejected;
use App\Notifications\ShiftRejectedNotification;
use App\Events\HoursApproved;
use App\Notifications\HoursApprovedNotification;
use App\Events\VolunteerRejected;
use App\Notifications\VolunteerRejectedNotification;
use App\Events\VolunteerSuspended;

class SendVolunteerNotification
{
    public function handleShiftApproved(ShiftApproved $event): void
    {
        $user = $event->request->volunteer?->user;
        $user?->notify(new ShiftApprovedNotification($event->request));
    }

    public function handleShiftRejected(ShiftRejected $event): void
    {
        $user = $event->request->volunteer?->user;
        $user?->notify(new ShiftRejectedNotification($event->request));
    }

    public function handleHoursApproved(HoursApproved $event): void
    {
        $user = $event->hourLog->volunteer?->user;
        $user?->notify(new HoursApprovedNotification($event->hourLog));
    }

    public function handleVolunteerRejected(VolunteerRejected $event): void
    {
        $user = $event->volunteer->user;
        $user?->notify(new VolunteerRejectedNotification($event->volunteer, $event->reason));
    }
}
