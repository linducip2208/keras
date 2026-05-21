<?php

namespace App\Filament\Resources\Objectives\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ObjectiveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('cycle_id')
                    ->required()
                    ->numeric(),
                TextInput::make('owner_employee_id')
                    ->numeric(),
                TextInput::make('parent_objective_id')
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('weight')
                    ->required()
                    ->numeric()
                    ->default(100.0),
                TextInput::make('progress_percent')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Toggle::make('is_public')
                    ->required(),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'completed' => 'Completed', 'canceled' => 'Canceled'])
                    ->default('draft')
                    ->required(),
            ]);
    }
}
