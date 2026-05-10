<?php

namespace App\Events;

use App\Models\VolunteerSlotRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShiftApproved
{
    use Dispatchable, SerializesModels;
    public function __construct(public VolunteerSlotRequest $request) {}
}
