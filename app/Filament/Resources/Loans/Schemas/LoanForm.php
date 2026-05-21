<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LoanForm
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
                TextInput::make('loan_no')
                    ->required(),
                Select::make('loan_type')
                    ->options([
            'cash_advance' => 'Cash advance',
            'short_term' => 'Short term',
            'long_term' => 'Long term',
            'cooperative' => 'Cooperative',
        ])
                    ->required(),
                TextInput::make('principal')
                    ->required()
                    ->numeric(),
                TextInput::make('interest_rate')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('tenor_months')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('installment_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('total_payable')
                    ->required()
                    ->numeric(),
                Textarea::make('purpose')
                    ->columnSpanFull(),
                TextInput::make('attachment_path'),
                TextInput::make('disbursement_run_id')
                    ->numeric(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'disbursed' => 'Disbursed',
            'active' => 'Active',
            'completed' => 'Completed',
            'defaulted' => 'Defaulted',
            'rejected' => 'Rejected',
        ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('disbursed_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
