<?php

namespace App\Filament\Resources\ExitRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExitRequestForm
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
                Select::make('exit_type')
                    ->options([
            'resignation' => 'Resignation',
            'termination' => 'Termination',
            'retirement' => 'Retirement',
            'end_of_contract' => 'End of contract',
            'deceased' => 'Deceased',
        ])
                    ->required(),
                DatePicker::make('notice_date')
                    ->required(),
                DatePicker::make('last_working_date')
                    ->required(),
                Textarea::make('reason')
                    ->columnSpanFull(),
                TextInput::make('reason_category'),
                Toggle::make('voluntary')
                    ->required(),
                Toggle::make('eligible_for_rehire')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'in_clearance' => 'In clearance',
            'completed' => 'Completed',
            'withdrawn' => 'Withdrawn',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('paklaring_letter_id')
                    ->numeric(),
            ]);
    }
}
