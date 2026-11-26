<?php

namespace App\Filament\Resources\BpjsCalculations\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BpjsCalculationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')->label('Karyawan')->searchable(),
                TextColumn::make('program')->label('Program')->badge(),
                TextColumn::make('basis_amount')->label('Basis')->money('IDR')->sortable(),
                TextColumn::make('employer_amount')->label('Perusahaan')->money('IDR')->sortable(),
                TextColumn::make('employee_amount')->label('Karyawan')->money('IDR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('program')
                    ->options(['kesehatan' => 'Kesehatan', 'jht' => 'JHT', 'jp' => 'JP', 'jkk' => 'JKK', 'jkm' => 'JKM']),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
