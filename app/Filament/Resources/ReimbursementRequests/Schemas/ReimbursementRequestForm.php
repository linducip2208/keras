<?php

namespace App\Filament\Resources\ReimbursementRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReimbursementRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('request_no')
                    ->required(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                Textarea::make('purpose')
                    ->columnSpanFull(),
                TextInput::make('business_trip_id')
                    ->numeric(),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
            'canceled' => 'Canceled',
        ])
                    ->default('draft')
                    ->required(),
                Select::make('disbursement_method')
                    ->options(['payroll' => 'Payroll', 'separate' => 'Separate'])
                    ->default('payroll')
                    ->required(),
                TextInput::make('disbursement_run_id')
                    ->numeric(),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('approved_at'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
