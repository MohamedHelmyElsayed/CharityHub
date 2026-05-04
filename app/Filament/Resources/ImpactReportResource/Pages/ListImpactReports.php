<?php

namespace App\Filament\Resources\ImpactReportResource\Pages;

use App\Filament\Resources\ImpactReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImpactReports extends ListRecords
{
    protected static string $resource = ImpactReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
