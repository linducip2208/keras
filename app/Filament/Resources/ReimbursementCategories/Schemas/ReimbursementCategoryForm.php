<?php

namespace App\Filament\Resources\ReimbursementCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReimbursementCategoryForm
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
                TextInput::make('slug')
                    ->required(),
                TextInput::make('max_per_request')
                    ->numeric(),
                Toggle::make('requires_receipt')
                    ->required(),
                Toggle::make('requires_approval')
                    ->required(),
                TextInput::make('gl_account_code'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
