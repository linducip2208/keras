<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('subscription_id')
                    ->numeric(),
                TextInput::make('number')
                    ->required(),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end')
                    ->required(),
                DatePicker::make('issue_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'open' => 'Open',
            'paid' => 'Paid',
            'past_due' => 'Past due',
            'void' => 'Void',
            'refunded' => 'Refunded',
        ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('paid_at'),
                TextInput::make('paid_via'),
                TextInput::make('pdf_path'),
                TextInput::make('line_items'),
            ]);
    }
}
