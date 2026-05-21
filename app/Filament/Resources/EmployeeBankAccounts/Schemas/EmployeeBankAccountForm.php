<?php

namespace App\Filament\Resources\EmployeeBankAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeBankAccountForm
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
                TextInput::make('bank_name')
                    ->required(),
                Textarea::make('account_number')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('account_holder_name')
                    ->required(),
                Toggle::make('is_primary')
                    ->required(),
                TextInput::make('allocation_percentage')
                    ->required()
                    ->numeric()
                    ->default(100.0),
            ]);
    }
}
