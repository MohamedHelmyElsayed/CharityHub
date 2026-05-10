<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HourLogResource\Pages;
use App\Models\HourLog;
use App\Services\HourCalculationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HourLogResource extends Resource
{
    protected static ?string $model = HourLog::class;
    protected static ?string $slug = 'hour-logs';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Volunteer Management';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Hour Logs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('volunteer_id')
                ->relationship('volunteer', 'name')->searchable()->preload()->required(),
            Forms\Components\TextInput::make('calculated_hours')->numeric()->required(),
            Forms\Components\TextInput::make('approved_hours')->numeric(),
            Forms\Components\Textarea::make('adjustment_reason'),
            Forms\Components\Select::make('status')->options([
                'pending_review' => 'Pending Review',
                'approved' => 'Approved',
                'adjusted' => 'Adjusted',
                'rejected' => 'Rejected',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('volunteer.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('attendanceLog.shift.title')->label('Shift'),
                Tables\Columns\TextColumn::make('calculated_hours')->label('Calculated')->numeric(2),
                Tables\Columns\TextColumn::make('approved_hours')->label('Approved')->numeric(2)->placeholder('—'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => 'pending_review',
                    'success' => 'approved',
                    'primary' => 'adjusted',
                    'danger'  => 'rejected',
                ]),
                Tables\Columns\TextColumn::make('approved_at')->dateTime()->label('Approved On'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending_review' => 'Pending Review',
                    'approved' => 'Approved',
                    'adjusted' => 'Adjusted',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (HourLog $r) => $r->status === 'pending_review')
                    ->action(function (HourLog $log) {
                        app(HourCalculationService::class)->approve($log, auth()->user());
                        Notification::make()->title('Hours approved!')->success()->send();
                    }),
                Tables\Actions\Action::make('adjust')
                    ->icon('heroicon-o-pencil-square')->color('warning')
                    ->visible(fn (HourLog $r) => $r->status === 'pending_review')
                    ->form([
                        Forms\Components\TextInput::make('adjusted_hours')->numeric()->required()->label('Adjusted Hours'),
                        Forms\Components\Textarea::make('reason')->required()->label('Reason'),
                    ])
                    ->action(function (HourLog $log, array $data) {
                        app(HourCalculationService::class)->approve($log, auth()->user(), (float)$data['adjusted_hours'], $data['reason']);
                        Notification::make()->title('Hours adjusted and approved.')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn (HourLog $r) => $r->status === 'pending_review')
                    ->form([Forms\Components\Textarea::make('reason')->required()->label('Rejection Reason')])
                    ->action(function (HourLog $log, array $data) {
                        app(HourCalculationService::class)->reject($log, auth()->user(), $data['reason']);
                        Notification::make()->title('Hours rejected.')->warning()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')->icon('heroicon-o-check')->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $svc = app(HourCalculationService::class);
                            foreach ($records->where('status', 'pending_review') as $log) {
                                $svc->approve($log, auth()->user());
                            }
                            Notification::make()->title('Bulk approval complete!')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHourLogs::route('/'),
            'edit'  => Pages\EditHourLog::route('/{record}/edit'),
        ];
    }
}
