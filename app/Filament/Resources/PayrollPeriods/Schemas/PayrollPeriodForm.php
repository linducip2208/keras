<?php

namespace App\Filament\Resources\PayrollPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PayrollPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Select::make('period_type')
                    ->options(['monthly' => 'Monthly', 'weekly' => 'Weekly', 'biweekly' => 'Biweekly', 'daily' => 'Daily'])
                    ->default('monthly')
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                DatePicker::make('cutoff_attendance_date')
                    ->required(),
                DatePicker::make('payroll_date')
                    ->required(),
                Select::make('status')
                    ->options([
            'open' => 'Open',
            'cutoff' => 'Cutoff',
            'processing' => 'Processing',
            'finalized' => 'Finalized',
            'paid' => 'Paid',
            'locked' => 'Locked',
        ])
                    ->default('open')
                    ->required(),
            ]);
    }
}
