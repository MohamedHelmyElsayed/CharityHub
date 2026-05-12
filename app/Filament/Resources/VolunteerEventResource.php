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
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Opportunities';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Opportunity Details')->schema([
                Forms\Components\TextInput::make('title')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Select::make('campaign_id')
                    ->relationship('campaign', 'title')->searchable()->preload()->nullable()
                    ->label('Related Campaign'),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('event_type')->options([
                        'general'      => 'General',
                        'fundraising'  => 'Fundraising',
                        'cleanup'      => 'Cleanup',
                        'education'    => 'Education',
                        'medical'      => 'Medical',
                        'construction' => 'Construction',
                        'food'         => 'Food Drive',
                        'community'    => 'Community',
                    ])->required()->label('Type'),
                    Forms\Components\Select::make('category')->options([
                        'general'      => 'General',
                        'fundraising'  => 'Fundraising',
                        'cleanup'      => 'Cleanup',
                        'education'    => 'Education',
                        'medical'      => 'Medical',
                        'food'         => 'Food Drive',
                        'community'    => 'Community',
                    ])->nullable()->label('Category Badge'),
                ]),
                Forms\Components\RichEditor::make('description')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('requirements')
                    ->label('Volunteer Requirements')->rows(3)->columnSpanFull()
                    ->placeholder('e.g. Must be 18+, physical fitness required, valid ID…'),
                Forms\Components\Textarea::make('benefits')
                    ->label('Volunteer Benefits')->rows(3)->columnSpanFull()
                    ->placeholder('e.g. Meals provided, certificate of appreciation, training workshop…'),
            ])->columns(2),

            Forms\Components\Section::make('Location & Schedule')->schema([
                Forms\Components\TextInput::make('location')->required(),
                Forms\Components\TextInput::make('max_volunteers')->numeric()->default(0)
                    ->helperText('0 = unlimited'),
                Forms\Components\DateTimePicker::make('start_date')->required(),
                Forms\Components\DateTimePicker::make('end_date')->required(),
                Forms\Components\DateTimePicker::make('registration_deadline')
                    ->label('Application Deadline')->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Skills & Media')->schema([
                Forms\Components\TagsInput::make('required_skills')
                    ->separator(',')->label('Required Skills'),
                Forms\Components\Select::make('status')->options([
                    'draft'     => 'Draft (not public)',
                    'open'      => 'Open (accepting applications)',
                    'full'      => 'Full',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])->required()->default('draft'),
                Forms\Components\FileUpload::make('cover_image')
                    ->image()->directory('volunteer-events')->label('Cover Image'),
                Forms\Components\FileUpload::make('banner_image')
                    ->image()->directory('volunteer-events')->label('Banner Image (detail page)'),
                Forms\Components\FileUpload::make('gallery')
                    ->image()->directory('volunteer-events/gallery')
                    ->multiple()->label('Gallery Images')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->circular()->label(''),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('category')->badge()->label('Category'),
                Tables\Columns\TextColumn::make('location')->limit(25),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable()->label('Starts'),
                Tables\Columns\TextColumn::make('registration_deadline')->date()->label('Deadline'),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')->label('Applications'),
                Tables\Columns\TextColumn::make('shifts_count')
                    ->counts('shifts')->label('Shifts'),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_public')
                    =>label('View Page')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (VolunteerEvent $record) => route('volunteering.show', $record->slug))
                    ->openUrlInNewTab(),
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
            'index'  => Pages\ListVolunteerEvents::route('/'),
            'create' => Pages\CreateVolunteerEvent::route('/create'),
            'edit'   => Pages\EditVolunteerEvent::route('/{record}/edit'),
        ];
    }
}
