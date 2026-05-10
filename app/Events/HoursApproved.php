<?php

namespace App\Events;

use App\Models\HourLog;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HoursApproved
{
    use Dispatchable, SerializesModels;
    public function __construct(public HourLog $hourLog) {}
}
