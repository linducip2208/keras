<?php

namespace App\Filament\Resources\EmployeeFamilies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeFamilyForm
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
                Select::make('relation')
                    ->options(['spouse' => 'Spouse', 'child' => 'Child', 'parent' => 'Parent', 'sibling' => 'Sibling'])
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('gender')
                    ->options(['M' => 'M', 'F' => 'F']),
                DatePicker::make('date_of_birth'),
                Textarea::make('nik')
                    ->columnSpanFull(),
                Toggle::make('is_bpjs_dependent')
                    ->required(),
                Toggle::make('is_ptkp_dependent')
                    ->required(),
            ]);
    }
}
