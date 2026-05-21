<?php

namespace App\Filament\Resources\PayrollRuns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PayrollRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('payroll_period_id')
                    ->required()
                    ->numeric(),
                Select::make('run_type')
                    ->options([
            'regular' => 'Regular',
            'off_cycle' => 'Off cycle',
            'bonus' => 'Bonus',
            'thr' => 'Thr',
            'correction' => 'Correction',
        ])
                    ->default('regular')
                    ->required(),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'calculating' => 'Calculating',
            'review' => 'Review',
            'approved' => 'Approved',
            'disbursed' => 'Disbursed',
            'finalized' => 'Finalized',
            'failed' => 'Failed',
        ])
                    ->default('draft')
                    ->required(),
                TextInput::make('total_employees')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_gross')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_net')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_tax')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_bpjs_employer')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_bpjs_employee')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                TextInput::make('approved_by_user_id')
                    ->numeric(),
                DateTimePicker::make('approved_at'),
                TextInput::make('created_by_user_id')
                    ->numeric(),
            ]);
    }
}
