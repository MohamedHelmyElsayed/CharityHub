<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialLogResource\Pages;
use App\Filament\Resources\FinancialLogResource\RelationManagers;
use App\Models\FinancialLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinancialLogResource extends Resource
{
    protected static ?string $model = FinancialLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialLogs::route('/'),
            'create' => Pages\CreateFinancialLog::route('/create'),
            'edit' => Pages\EditFinancialLog::route('/{record}/edit'),
        ];
    }
}
