<?php

namespace App\Filament\Resources\FinancialLogResource\Pages;

use App\Filament\Resources\FinancialLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialLog extends EditRecord
{
    protected static string $resource = FinancialLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
