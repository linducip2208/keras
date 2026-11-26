<?php

namespace App\Filament\Resources\TaxCalculations\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaxCalculationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_year')->label('Tahun')->sortable(),
                TextColumn::make('period_month')->label('Bulan')->sortable(),
                TextColumn::make('employee.full_name')->label('Karyawan')->searchable(),
                TextColumn::make('gross_basis')->label('Bruto TER')->money('IDR')->sortable(),
                TextColumn::make('ter_category')->label('Kat')->badge(),
                TextColumn::make('ter_rate')->label('Rate %')->numeric(),
                TextColumn::make('pph_21_amount')->label('PPh 21')->money('IDR')->sortable(),
                TextColumn::make('method_used')->label('Metode')->badge(),
            ])
            ->filters([
                SelectFilter::make('period_year')->label('Tahun')
                    ->options(fn () => \App\Models\TaxCalculation::query()->distinct()->pluck('period_year', 'period_year')->toArray()),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
