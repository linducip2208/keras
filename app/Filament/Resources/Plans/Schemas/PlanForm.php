<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('max_employees')
                    ->numeric(),
                TextInput::make('price_monthly')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('price_annual')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                TextInput::make('features'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
