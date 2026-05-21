<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('depreciation_method')
                    ->options(['straight_line' => 'Straight line', 'none' => 'None'])
                    ->default('straight_line')
                    ->required(),
                TextInput::make('useful_life_years')
                    ->numeric(),
                TextInput::make('gl_account_code'),
            ]);
    }
}
