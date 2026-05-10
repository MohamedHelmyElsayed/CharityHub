<?php

namespace App\Filament\Resources;

use App\Events\VolunteerCheckedOut;
use App\Filament\Resources\AttendanceLogResource\Pages;
use App\Models\AttendanceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;
    protected static ?string $slug = 'attendance-logs';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('volunteer_id')
                ->relationship('volunteer', 'name')->searchable()->preload()->required(),
            Forms\Components\Select::make('shift_id')
                ->relationship('shift', 'title')->searchable()->preload()->required(),
            Forms\Components\DateTimePicker::make('check_in')->required(),
            Forms\Components\DateTimePicker::make('check_out'),
            Forms\Components\Select::make('check_in_method')->options([
                'manual' => 'Manual', 'qr_code' => 'QR Code', 'self' => 'Self',
            ])->required()->default('manual'),
            Forms\Components\Select::make('status')->options([
                'checked_in' => 'Checked In', 'checked_out' => 'Checked Out',
                'verified' => 'Verified', 'disputed' => 'Disputed', 'absent' => 'Absent',
            ])->required()->default('checked_in'),
            Forms\Components\Textarea::make('notes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('check_in', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('volunteer.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('shift.title')->label('Shift'),
                Tables\Columns\TextColumn::make('check_in')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('check_out')->dateTime()->placeholder('—'),
                Tables\Columns\TextColumn::make('calculated_hours')->label('Hours')
                    ->getStateUsing(fn ($record) => $record->calculated_hours),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => 'checked_in', 'primary' => 'checked_out',
                    'success' => 'verified', 'danger' => 'disputed', 'gray' => 'absent',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('checkout')
                    ->icon('heroicon-o-arrow-right-circle')->color('primary')->label('Check Out')
                    ->visible(fn ($record) => $record->status === 'checked_in')
                    ->requiresConfirmation()
                    ->action(function (AttendanceLog $log) {
                        $log->update(['check_out' => now(), 'check_out_method' => 'manual', 'status' => 'checked_out']);
                        event(new VolunteerCheckedOut($log));
                        Notification::make()->title('Checked out. Hours queued for review.')->success()->send();
                    }),
                Tables\Actions\Action::make('verify')
                    ->icon('heroicon-o-shield-check')->color('success')
                    ->visible(fn ($record) => $record->status === 'checked_out')
                    ->action(function (AttendanceLog $log) {
                        $log->update(['status' => 'verified', 'verified_by' => auth()->id()]);
                        Notification::make()->title('Attendance verified.')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttendanceLogs::route('/'),
            'create' => Pages\CreateAttendanceLog::route('/create'),
            'edit'   => Pages\EditAttendanceLog::route('/{record}/edit'),
        ];
    }
}
