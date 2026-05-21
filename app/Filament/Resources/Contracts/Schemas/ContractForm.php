<?php

namespace App\Filament\Resources\Contracts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContractForm
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
                Select::make('contract_type')
                    ->options([
            'PKWT' => 'P k w t',
            'PKWTT' => 'P k w t t',
            'probation' => 'Probation',
            'intern' => 'Intern',
            'outsource' => 'Outsource',
            'freelance' => 'Freelance',
            'daily' => 'Daily',
        ])
                    ->required(),
                TextInput::make('contract_no')
                    ->required(),
                TextInput::make('template_id')
                    ->numeric(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                DatePicker::make('probation_end_date'),
                TextInput::make('salary_base')
                    ->required()
                    ->numeric(),
                TextInput::make('salary_currency')
                    ->required()
                    ->default('IDR'),
                TextInput::make('job_title_id')
                    ->numeric(),
                TextInput::make('position_id')
                    ->numeric(),
                TextInput::make('department_id')
                    ->numeric(),
                TextInput::make('branch_id')
                    ->numeric(),
                TextInput::make('working_hours_per_week')
                    ->required()
                    ->numeric()
                    ->default(40),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'active' => 'Active',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'renewed' => 'Renewed',
        ])
                    ->default('draft')
                    ->required(),
                TextInput::make('parent_contract_id')
                    ->numeric(),
                TextInput::make('file_path'),
                DateTimePicker::make('signed_at'),
                DateTimePicker::make('signed_by_employee_at'),
                DateTimePicker::make('signed_by_company_at'),
                TextInput::make('e_sign_provider'),
                TextInput::make('e_sign_reference_id'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
