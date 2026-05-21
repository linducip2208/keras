<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartmentForm
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
                TextInput::make('parent_department_id')
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('cost_center_code'),
                TextInput::make('headcount_budget')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('head_employee_id')
                    ->numeric(),
            ]);
    }
}
