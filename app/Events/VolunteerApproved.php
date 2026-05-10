<?php

namespace App\Events;

use App\Models\Volunteer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VolunteerApproved
{
    use Dispatchable, SerializesModels;
    public function __construct(public Volunteer $volunteer) {}
}
