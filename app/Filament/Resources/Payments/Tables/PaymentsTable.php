<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label('Ref')->searchable()->copyable(),
                TextColumn::make('tenant.name')->label('Tenant')->searchable()->toggleable(),
                TextColumn::make('invoice.number')->label('Invoice')->searchable(),
                TextColumn::make('adapter.name')->label('Gateway')->badge()->color('info'),
                TextColumn::make('amount')->label('Nominal')->money(fn ($r) => $r->currency ?? 'IDR')->sortable(),
                TextColumn::make('payment_method')->label('Method')->badge(),
                TextColumn::make('payment_channel')->label('Channel')->badge()->toggleable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match($state) {
                        'paid' => 'success', 'pending' => 'warning',
                        'failed', 'expired' => 'danger',
                        'refunded' => 'info', default => 'gray',
                    }),
                TextColumn::make('va_number')->label('VA Number')->copyable()->toggleable(),
                TextColumn::make('expires_at')->label('Expire')->dateTime('d M H:i')->toggleable(),
                TextColumn::make('paid_at')->label('Dibayar')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending'=>'Pending','paid'=>'Paid','failed'=>'Failed',
                    'expired'=>'Expired','refunded'=>'Refunded',
                ]),
                SelectFilter::make('payment_method')->options([
                    'qris'=>'QRIS','va'=>'Virtual Account','ewallet'=>'E-Wallet',
                    'credit_card'=>'Credit Card','bank_transfer_manual'=>'Manual Bank Transfer',
                    'paypal'=>'PayPal',
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
