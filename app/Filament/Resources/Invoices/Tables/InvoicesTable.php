<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('Invoice #')->searchable()->sortable(),
                TextColumn::make('tenant.name')->label('Tenant')->searchable(),
                TextColumn::make('period_start')->label('Periode')
                    ->formatStateUsing(fn ($state, $record) => $state->format('d M') . ' – ' . $record->period_end->format('d M Y')),
                TextColumn::make('subtotal')->label('Subtotal')->money('IDR'),
                TextColumn::make('tax_amount')->label('PPN 11%')->money('IDR')->toggleable(),
                TextColumn::make('total_amount')->label('Total')->money('IDR')->sortable()
                    ->weight('bold'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match($state) {
                        'paid' => 'success', 'open' => 'warning',
                        'past_due' => 'danger', 'void' => 'gray',
                        'refunded' => 'info', 'draft' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y')->sortable()
                    ->color(fn ($state, $record) => $record->status === 'open' && now()->gt($state) ? 'danger' : null),
                TextColumn::make('paid_at')->label('Dibayar')->dateTime('d M Y H:i')->toggleable(),
                TextColumn::make('paid_via')->label('Via')->badge()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft'=>'Draft','open'=>'Open','paid'=>'Paid',
                    'past_due'=>'Past Due','void'=>'Void','refunded'=>'Refunded',
                ]),
            ])
            ->defaultSort('issue_date', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
