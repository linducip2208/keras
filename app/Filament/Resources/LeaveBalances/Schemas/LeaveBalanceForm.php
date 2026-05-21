<?php

namespace App\Filament\Resources\LeaveBalances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeaveBalanceForm
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
                TextInput::make('leave_type_id')
                    ->required()
                    ->numeric(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('allocated')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('used')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('carried_over')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('pending')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DatePicker::make('expires_at'),
            ]);
    }
}
