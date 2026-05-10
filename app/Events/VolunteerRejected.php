<?php

namespace App\Events;

use App\Models\Volunteer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VolunteerRejected
{
    use Dispatchable, SerializesModels;
    public function __construct(public Volunteer $volunteer, public ?string $reason = null) {}
}
