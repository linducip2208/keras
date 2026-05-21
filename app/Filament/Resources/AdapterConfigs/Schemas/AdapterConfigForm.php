<?php

namespace App\Filament\Resources\AdapterConfigs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdapterConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('domain')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('format')
                    ->required(),
                TextInput::make('base_url')
                    ->url(),
                Textarea::make('api_key_encrypted')
                    ->columnSpanFull(),
                Textarea::make('api_secret_encrypted')
                    ->columnSpanFull(),
                TextInput::make('extra_headers'),
                TextInput::make('extra_config'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_test_mode')
                    ->required(),
                DateTimePicker::make('last_tested_at'),
                TextInput::make('last_test_status'),
            ]);
    }
}
