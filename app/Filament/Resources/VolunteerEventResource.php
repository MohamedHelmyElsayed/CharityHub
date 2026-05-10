<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolunteerEventResource\Pages;
use App\Models\VolunteerEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VolunteerEventResource extends Resource
{
    protected static ?string $model = VolunteerEvent::class;
    protected static ?string $slug = 'volunteer-events';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Events';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event Details')->schema([
                Forms\Components\TextInput::make('title')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Select::make('campaign_id')
                    ->relationship('campaign', 'title')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('event_type')->options([
                    'general'       => 'General',
                    'fundraising'   => 'Fundraising',
                    'cleanup'       => 'Cleanup',
                    'education'     => 'Education',
                    'medical'       => 'Medical',
                    'construction'  => 'Construction',
                    'food_drive'    => 'Food Drive',
                ])->required(),
                Forms\Components\RichEditor::make('description')->required()->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Location & Capacity')->schema([
                Forms\Components\TextInput::make('location')->required(),
                Forms\Components\TextInput::make('max_volunteers')->numeric()->default(0)
                    ->helperText('0 = unlimited'),
                Forms\Components\DateTimePicker::make('registration_deadline'),
                Forms\Components\DateTimePicker::make('start_date')->required(),
                Forms\Components\DateTimePicker::make('end_date')->required(),
            ])->columns(2),
            Forms\Components\Section::make('Skills & Status')->schema([
                Forms\Components\TagsInput::make('required_skills')->separator(','),
                Forms\Components\Select::make('status')->options([
                    'draft'     => 'Draft',
                    'open'      => 'Open',
                    'full'      => 'Full',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])->required()->default('draft'),
                Forms\Components\FileUpload::make('cover_image')
                    ->image()->directory('volunteer-events')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->circular()->label(''),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('event_type')->badge(),
                Tables\Columns\TextColumn::make('start_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('shifts_count')->counts('shifts')->label('Shifts'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'draft',
                        'success' => 'open',
                        'warning' => 'full',
                        'primary' => 'completed',
                        'danger'  => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'open' => 'Open',
                    'full'  => 'Full',  'completed' => 'Completed', 'cancelled' => 'Cancelled',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVolunteerEvents::route('/'),
            'create' => Pages\CreateVolunteerEvent::route('/create'),
            'edit'   => Pages\EditVolunteerEvent::route('/{record}/edit'),
        ];
    }
}
