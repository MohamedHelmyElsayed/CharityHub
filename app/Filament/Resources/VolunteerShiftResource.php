<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolunteerShiftResource\Pages;
use App\Models\VolunteerShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VolunteerShiftResource extends Resource
{
    protected static ?string $model = VolunteerShift::class;
    protected static ?string $slug = 'volunteer-shifts';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Shifts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Shift Details')->schema([
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'title')->searchable()->preload()->required(),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Textarea::make('description')->rows(2),
                Forms\Components\TextInput::make('location'),
            ])->columns(2),
            Forms\Components\Section::make('Schedule & Capacity')->schema([
                Forms\Components\DatePicker::make('shift_date')->required(),
                Forms\Components\TimePicker::make('start_time')->required(),
                Forms\Components\TimePicker::make('end_time')->required(),
                Forms\Components\TextInput::make('required_volunteers')->numeric()->default(1)->required(),
            ])->columns(2),
            Forms\Components\Section::make('Status')->schema([
                Forms\Components\Select::make('status')->options([
                    'open'      => 'Open',
                    'full'      => 'Full',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])->required()->default('open'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')->label('Event')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('shift_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('assigned_count')->label('Assigned'),
                Tables\Columns\TextColumn::make('required_volunteers')->label('Required'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'open',
                        'warning' => 'full',
                        'primary' => 'completed',
                        'danger'  => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'open' => 'Open', 'full' => 'Full',
                    'completed' => 'Completed', 'cancelled' => 'Cancelled',
                ]),
                Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'title')->label('Event'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVolunteerShifts::route('/'),
            'create' => Pages\CreateVolunteerShift::route('/create'),
            'edit'   => Pages\EditVolunteerShift::route('/{record}/edit'),
        ];
    }
}
