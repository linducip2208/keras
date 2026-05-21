<?php

namespace App\Filament\Resources\PayrollItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('payroll_run_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('employee_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('basic_salary')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gross')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_earnings')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_deductions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_benefits')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pph_21')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bpjs_employer')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bpjs_employee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_pay')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('present_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('absent_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leave_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('prorate_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('prorate_factor')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('calculated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
