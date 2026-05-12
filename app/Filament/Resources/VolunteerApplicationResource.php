<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolunteerApplicationResource\Pages;
use App\Models\VolunteerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VolunteerApplicationResource extends Resource
{
    protected static ?string $model = VolunteerApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Opportunity Applications';
    protected static ?string $slug = 'volunteer-applications';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('event_id')
                ->relationship('event', 'title')
                ->label('Opportunity')
                ->searchable()->preload()->required(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Applicant')
                ->searchable()->preload()->required(),
            Forms\Components\Select::make('status')
                ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                ->required(),
            Forms\Components\Textarea::make('motivation')->label('Motivation')->rows(3),
            Forms\Components\Textarea::make('skills_offered')->label('Skills Offered')->rows(2),
            Forms\Components\Textarea::make('experience')->label('Experience')->rows(2),
            Forms\Components\TextInput::make('availability')->label('Availability'),
            Forms\Components\Textarea::make('admin_notes')->label('Admin Notes (internal)')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Applicant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Opportunity')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('availability')
                    ->label('Availability')->limit(30),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->label('Applied')->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()->label('Reviewed')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'title')
                    ->label('Opportunity')
                    ->searchable()->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->label('Approve')
                    ->visible(fn (VolunteerApplication $r) => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Internal Notes (optional)')->rows(2),
                    ])
                    ->action(function (VolunteerApplication $application, array $data) {
                        $application->approve(auth()->id(), $data['admin_notes'] ?? null);
                        Notification::make()->title('Application approved!')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->label('Reject')
                    ->visible(fn (VolunteerApplication $r) => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Reason for Rejection')->required()->rows(2),
                    ])
                    ->action(function (VolunteerApplication $application, array $data) {
                        $application->reject(auth()->id(), $data['admin_notes']);
                        Notification::make()->title('Application rejected.')->warning()->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $application) {
                                if ($application->status === 'pending') {
                                    $application->approve(auth()->id());
                                }
                            }
                            Notification::make()->title('Selected applications approved!')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVolunteerApplications::route('/'),
            'view'   => Pages\ViewVolunteerApplication::route('/{record}'),
            'edit'   => Pages\EditVolunteerApplication::route('/{record}/edit'),
        ];
    }
}
