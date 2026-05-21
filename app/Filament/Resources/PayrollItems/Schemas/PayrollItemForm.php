<?php

namespace App\Filament\Resources\PayrollItems\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PayrollItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('payroll_run_id')
                    ->required()
                    ->numeric(),
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('basic_salary')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('gross')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_earnings')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_deductions')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_benefits')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('pph_21')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('bpjs_employer')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('bpjs_employee')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('net_pay')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('working_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('present_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('absent_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('leave_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('overtime_hours')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('overtime_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('prorate_days')
                    ->numeric(),
                TextInput::make('prorate_factor')
                    ->required()
                    ->numeric()
                    ->default(1.0),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'calculated' => 'Calculated',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'hold' => 'Hold',
        ])
                    ->default('pending')
                    ->required(),
                Textarea::make('hold_reason')
                    ->columnSpanFull(),
                DateTimePicker::make('calculated_at'),
            ]);
    }
}
