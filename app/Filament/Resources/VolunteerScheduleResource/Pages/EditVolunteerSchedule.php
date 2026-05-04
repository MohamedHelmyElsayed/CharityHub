<?php

namespace App\Filament\Resources\VolunteerScheduleResource\Pages;

use App\Filament\Resources\VolunteerScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerSchedule extends EditRecord
{
    protected static string $resource = VolunteerScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
