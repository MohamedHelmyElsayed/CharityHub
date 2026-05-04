<?php

namespace App\Filament\Resources\VolunteerScheduleResource\Pages;

use App\Filament\Resources\VolunteerScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerSchedules extends ListRecords
{
    protected static string $resource = VolunteerScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
