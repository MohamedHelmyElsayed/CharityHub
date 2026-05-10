<?php

namespace App\Filament\Resources;

use App\Events\VolunteerApproved;
use App\Events\VolunteerRejected;
use App\Events\VolunteerSuspended;
use App\Filament\Resources\VolunteerResource\Pages;
use App\Models\Volunteer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VolunteerResource extends Resource
{
    protected static ?string $model = Volunteer::class;
    protected static ?string $slug = 'volunteer-records';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Volunteers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profile')->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()->preload()->label('User Account'),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\DatePicker::make('date_of_birth')->label('Date of Birth'),
                Forms\Components\Select::make('gender')->options([
                    'male' => 'Male', 'female' => 'Female',
                    'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say',
                ])->nullable(),
                Forms\Components\Textarea::make('address')->rows(2),
                Forms\Components\TagsInput::make('skills')->separator(','),
                Forms\Components\TagsInput::make('interests')->separator(','),
                Forms\Components\Textarea::make('bio')->rows(3),
            ])->columns(2),
            Forms\Components\Section::make('Emergency Contact')->schema([
                Forms\Components\TextInput::make('emergency_contact_name')->label('Name'),
                Forms\Components\TextInput::make('emergency_contact_phone')->label('Phone'),
            ])->columns(2),
            Forms\Components\Section::make('Admin Notes')->schema([
                Forms\Components\Textarea::make('internal_notes')->label('Internal Notes (Admin Only)')->rows(3),
            ])->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => fn ($s) => in_array($s, ['approved', 'active']),
                        'danger'  => fn ($s) => in_array($s, ['rejected', 'suspended']),
                        'gray'    => 'inactive',
                    ]),
                Tables\Columns\TextColumn::make('total_approved_hours')
                    ->label('Approved Hours')->numeric(2)->sortable(),
                Tables\Columns\TextColumn::make('slotRequests_count')
                    ->counts('slotRequests')->label('Requests'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending'   => 'Pending',
                    'approved'  => 'Approved',
                    'rejected'  => 'Rejected',
                    'suspended' => 'Suspended',
                    'inactive'  => 'Inactive',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Volunteer $v) => $v->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Volunteer $volunteer) {
                        $volunteer->approve(auth()->user());
                        event(new VolunteerApproved($volunteer));
                        Notification::make()->title('Volunteer approved!')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn (Volunteer $v) => $v->status === 'pending')
                    ->form([Forms\Components\Textarea::make('reason')->label('Rejection Reason')])
                    ->action(function (Volunteer $volunteer, array $data) {
                        $volunteer->reject(auth()->user(), $data['reason'] ?? null);
                        event(new VolunteerRejected($volunteer, $data['reason'] ?? null));
                        Notification::make()->title('Volunteer rejected.')->warning()->send();
                    }),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')->color('warning')
                    ->visible(fn (Volunteer $v) => in_array($v->status, ['approved', 'active']))
                    ->form([Forms\Components\Textarea::make('reason')->label('Suspension Reason')])
                    ->action(function (Volunteer $volunteer, array $data) {
                        $volunteer->suspend(auth()->user(), $data['reason'] ?? null);
                        event(new VolunteerSuspended($volunteer, $data['reason'] ?? null));
                        Notification::make()->title('Volunteer suspended.')->warning()->send();
                    }),
                Tables\Actions\Action::make('reactivate')
                    ->icon('heroicon-o-arrow-path')->color('success')
                    ->visible(fn (Volunteer $v) => in_array($v->status, ['suspended', 'inactive']))
                    ->action(function (Volunteer $volunteer) {
                        $volunteer->reactivate();
                        Notification::make()->title('Volunteer reactivated!')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $volunteer) {
                                if ($volunteer->status === 'pending') {
                                    $volunteer->approve(auth()->user());
                                    event(new VolunteerApproved($volunteer));
                                }
                            }
                            Notification::make()->title('Selected volunteers approved!')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVolunteers::route('/'),
            'create' => Pages\CreateVolunteer::route('/create'),
            'edit'   => Pages\EditVolunteer::route('/{record}/edit'),
        ];
    }
}
