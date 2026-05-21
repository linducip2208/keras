<?php

namespace App\Filament\Resources\OvertimeRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OvertimeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('employee_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('planned_start')
                    ->time()
                    ->sortable(),
                TextColumn::make('planned_end')
                    ->time()
                    ->sortable(),
                TextColumn::make('actual_start')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('actual_end')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('planned_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('actual_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attachment_path')
                    ->searchable(),
                TextColumn::make('overtime_type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('amount_calculated')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('use_compensatory_leave')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
