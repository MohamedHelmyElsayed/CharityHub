<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialLogResource\Pages;
use App\Models\FinancialLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class FinancialLogResource extends Resource
{
    protected static ?string $model = FinancialLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    
    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?string $slug = 'audit-logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_type')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->disabled(),
                        Forms\Components\TextInput::make('idempotency_key')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Actor Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\Textarea::make('user_agent')
                            ->disabled(),
                    ])->columns(1),

                Forms\Components\Section::make('Audit Metadata')
                    ->schema([
                        Forms\Components\JsonEditor::make('metadata')
                            ->disabled(),
                        Forms\Components\JsonEditor::make('old_values')
                            ->disabled(),
                        Forms\Components\JsonEditor::make('new_values')
                            ->disabled(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Timestamp')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('transaction_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'payment_success', 'subscription_renewed', 'subscription_created' => 'success',
                        'payment_failed', 'webhook_failed', 'duplicate_request_blocked', 'renewal_failed' => 'danger',
                        'refund_issued', 'subscription_cancelled' => 'warning',
                        'manual_admin_adjustment' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('donor.name')
                    ->label('Donor')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'blocked' => 'danger',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('gateway')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('hash_verified')
                    ->label('Integrity')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->verifyHash())
                    ->tooltip('Verifies that the record has not been tampered with since creation.'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('gateway')
                    ->options([
                        'stripe' => 'Stripe',
                        'paymob' => 'PayMob',
                        'manual' => 'Manual',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'blocked' => 'Blocked',
                    ]),
                SelectFilter::make('transaction_type')
                    ->options([
                        'payment_success' => 'Payment Success',
                        'payment_failed' => 'Payment Failed',
                        'refund_issued' => 'Refund Issued',
                        'subscription_created' => 'Subscription Created',
                        'subscription_renewed' => 'Subscription Renewed',
                        'subscription_cancelled' => 'Subscription Cancelled',
                        'renewal_failed' => 'Renewal Failed',
                        'manual_admin_adjustment' => 'Manual Adjustment',
                        'webhook_received' => 'Webhook Received',
                        'duplicate_request_blocked' => 'Duplicate Blocked',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialLogs::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
