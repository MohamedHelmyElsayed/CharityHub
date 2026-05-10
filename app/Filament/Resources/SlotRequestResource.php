<?php

namespace App\Filament\Resources;

use App\Events\ShiftApproved;
use App\Events\ShiftRejected;
use App\Filament\Resources\SlotRequestResource\Pages;
use App\Models\VolunteerSlotRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlotRequestResource extends Resource
{
    protected static ?string $model = VolunteerSlotRequest::class;
    protected static ?string $slug = 'slot-requests';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Slot Requests';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('volunteer_id')
                ->relationship('volunteer', 'name')->searchable()->preload()->required(),
            Forms\Components\Select::make('shift_id')
                ->relationship('shift', 'title')->searchable()->preload()->required(),
            Forms\Components\Select::make('status')->options([
                'pending'   => 'Pending',
                'approved'  => 'Approved',
                'rejected'  => 'Rejected',
                'cancelled' => 'Cancelled',
                'completed' => 'Completed',
            ])->required(),
            Forms\Components\Textarea::make('notes')->label("Volunteer's Notes"),
            Forms\Components\Textarea::make('rejection_reason')->label('Rejection Reason'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('volunteer.name')->searchable()->sortable()->label('Volunteer'),
                Tables\Columns\TextColumn::make('shift.event.title')->label('Event')->searchable(),
                Tables\Columns\TextColumn::make('shift.title')->label('Shift'),
                Tables\Columns\TextColumn::make('shift.shift_date')->date()->label('Date')->sortable(),
                Tables\Columns\TextColumn::make('shift.start_time')->label('Start'),
                Tables\Columns\TextColumn::make('shift.end_time')->label('End'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                        'gray'    => fn ($s) => in_array($s, ['cancelled', 'completed']),
                    ]),
                Tables\Columns\TextColumn::make('requested_at')->dateTime()->label('Requested')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending'  => 'Pending', 'approved' => 'Approved',
                    'rejected' => 'Rejected', 'cancelled' => 'Cancelled',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')->color('success')
                    ->visible(fn (VolunteerSlotRequest $r) => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (VolunteerSlotRequest $request) {
                        $request->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id()]);
                        $request->shift?->incrementAssignedCount();
                        event(new ShiftApproved($request));
                        Notification::make()->title('Request approved!')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')->color('danger')
                    ->visible(fn (VolunteerSlotRequest $r) => $r->status === 'pending')
                    ->form([Forms\Components\Textarea::make('rejection_reason')->label('Reason')->required()])
                    ->action(function (VolunteerSlotRequest $request, array $data) {
                        $request->update([
                            'status' => 'rejected', 'rejected_at' => now(),
                            'rejected_by' => auth()->id(), 'rejection_reason' => $data['rejection_reason'],
                        ]);
                        event(new ShiftRejected($request));
                        Notification::make()->title('Request rejected.')->warning()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $request) {
                                if ($request->status === 'pending') {
                                    $request->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id()]);
                                    $request->shift?->incrementAssignedCount();
                                    event(new ShiftApproved($request));
                                }
                            }
                            Notification::make()->title('Selected requests approved!')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSlotRequests::route('/'),
            'edit'   => Pages\EditSlotRequest::route('/{record}/edit'),
        ];
    }
}
