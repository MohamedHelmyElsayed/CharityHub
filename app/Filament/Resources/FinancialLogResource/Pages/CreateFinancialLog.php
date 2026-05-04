<?php

namespace App\Filament\Resources\FinancialLogResource\Pages;

use App\Filament\Resources\FinancialLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialLog extends CreateRecord
{
    protected static string $resource = FinancialLogResource::class;
}
