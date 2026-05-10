<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use App\Models\Refund;
use App\Contracts\PaymentGatewayInterface;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Financial Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('donor_id')
                            ->relationship('donor', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('campaign_id')
                            ->relationship('campaign', 'title')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->label('Billing Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->label('Gateway Transaction ID')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Transaction Date')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('donor.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('campaign.title')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'recurring' => 'info',
                        'one_time' => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('gateway')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'one_time' => 'One-time',
                        'recurring' => 'Recurring',
                    ]),
                SelectFilter::make('gateway')
                    ->options([
                        'stripe' => 'Stripe',
                        'paymob' => 'PayMob',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('amount_range')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')->numeric(),
                        Forms\Components\TextInput::make('max_amount')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('refund')
                    ->label('Refund')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (Donation $record) => $record->status === 'completed' && !$record->isRefunded())
                    ->form([
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->required()
                            ->default(fn (Donation $record) => $record->amount)
                            ->maxValue(fn (Donation $record) => $record->amount)
                            ->prefix('$'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for Refund')
                            ->required(),
                    ])
                    ->action(function (Donation $record, array $data, PaymentGatewayInterface $gateway) {
                        $result = $gateway->refundCharge(
                            $record->stripe_payment_intent_id ?? $record->gateway_transaction_id,
                            (float) $data['refund_amount'],
                            $data['reason']
                        );

                        if ($result['status'] === 'success') {
                            $record->update([
                                'status' => 'refunded',
                                'refunded_at' => now(),
                                'gateway_refund_id' => $result['gateway_refund_id'],
                            ]);

                            Refund::create([
                                'donation_id' => $record->id,
                                'user_id' => auth()->id(),
                                'amount' => (float) $data['refund_amount'],
                                'currency' => $record->currency,
                                'reason' => $data['reason'],
                                'gateway_refund_id' => $result['gateway_refund_id'],
                                'status' => 'completed',
                            ]);

                            // Fire internal event for side effects (ledger, progress, etc.)
                            event(new \App\Events\RefundIssued($record, $result['data']));

                            Notification::make()
                                ->title('Refund Processed Successfully')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Refund Failed')
                                ->body($result['message'] ?? 'Gateway error')
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListDonations::route('/'),
            'view' => Pages\ViewDonation::route('/{record}'),
        ];
    }
}
