<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
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

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Financial Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\TextInput::make('gateway_subscription_id')
                            ->disabled()
                            ->label('Gateway Subscription ID'),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->disabled()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('billing_interval')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('next_billing_date')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('cancelled_at')
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
                    ->searchable()
                    ->limit(30),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('billing_interval')
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'warning',
                        'past_due' => 'danger',
                        'canceled', 'incomplete_expired' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('gateway')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('next_billing_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'canceled' => 'Canceled',
                        'past_due' => 'Past Due',
                    ]),
                SelectFilter::make('gateway')
                    ->options([
                        'stripe' => 'Stripe',
                        'paymob' => 'PayMob',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('sync')
                    ->label('Sync Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Subscription $record, PaymentGatewayInterface $gateway) {
                        $data = $gateway->getSubscription($record->gateway_subscription_id);
                        
                        if ($data) {
                            // Update status and next billing date based on gateway data
                            // This depends on normalized gateway data format
                            $status = $record->gateway === 'stripe' ? $data['status'] : $record->status;
                            $nextBilling = $record->gateway === 'stripe' ? \Carbon\Carbon::createFromTimestamp($data['current_period_end']) : $record->next_billing_date;

                            $record->update([
                                'status' => $status,
                                'next_billing_date' => $nextBilling,
                            ]);

                            Notification::make()
                                ->title('Subscription Synced')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Sync Failed')
                                ->body('Could not retrieve data from gateway.')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record) => $record->isActive())
                    ->action(function (Subscription $record, PaymentGatewayInterface $gateway) {
                        $success = $gateway->cancelSubscription($record->gateway_subscription_id);
                        
                        if ($success) {
                            $record->update([
                                'status' => 'canceled',
                                'cancelled_at' => now(),
                            ]);
                            
                            Notification::make()
                                ->title('Subscription Cancelled')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Cancellation Failed')
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
            RelationManagers\DonationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'view' => Pages\ViewSubscription::route('/{record}'),
        ];
    }
}
