<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('Tenant')->searchable(),
                TextColumn::make('plan.name')->label('Plan')->badge()->color('info'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success', 'trial' => 'info',
                        'past_due' => 'warning', 'canceled', 'expired', 'suspended' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('billing_cycle')->label('Siklus')->badge(),
                TextColumn::make('seats')->label('Seats')->numeric()->sortable(),
                TextColumn::make('current_period_start')->label('Mulai')->date('d M Y'),
                TextColumn::make('current_period_end')->label('Berakhir')->date('d M Y')
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                IconColumn::make('auto_renew')->label('Auto-renew')->boolean(),
                TextColumn::make('trial_ends_at')->label('Trial End')->date('d M Y')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'trial'=>'Trial','active'=>'Active','past_due'=>'Past Due',
                    'canceled'=>'Canceled','expired'=>'Expired','suspended'=>'Suspended',
                ]),
                SelectFilter::make('plan_id')->relationship('plan', 'name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
