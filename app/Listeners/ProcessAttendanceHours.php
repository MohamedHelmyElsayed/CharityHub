<?php

namespace App\Listeners;

use App\Events\VolunteerCheckedOut;
use App\Services\HourCalculationService;

class ProcessAttendanceHours
{
    public function __construct(private HourCalculationService $hourService) {}

    public function handle(VolunteerCheckedOut $event): void
    {
        $log = $event->log;
        if ($log->check_out) {
            $this->hourService->calculateFromAttendance($log);
        }
    }
}
