<?php

namespace App\Filament\Resources\Plans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_order')->label('#')->sortable(),
                TextColumn::make('name')->label('Plan')->searchable()
                    ->description(fn ($record) => $record->description),
                TextColumn::make('max_employees')->label('Max Karyawan')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : 'Unlimited'),
                TextColumn::make('price_monthly')->label('Harga / Bulan')->money('IDR')->sortable()
                    ->description(fn ($record) => 'per karyawan'),
                TextColumn::make('price_annual')->label('Harga / Tahun')->money('IDR')->sortable()
                    ->description('save 15%')
                    ->toggleable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('subscriptions_count')->label('Subscribers')
                    ->counts('subscriptions')->toggleable(),
            ])
            ->defaultSort('display_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
