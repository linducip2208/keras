<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
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
                TextInput::make('legal_name'),
                TextInput::make('npwp'),
                TextInput::make('npp_bpjs_kesehatan'),
                TextInput::make('npp_bpjs_naker'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('logo_path'),
                TextInput::make('parent_company_id')
                    ->numeric(),
                TextInput::make('country')
                    ->required()
                    ->default('ID'),
                TextInput::make('timezone')
                    ->required()
                    ->default('Asia/Jakarta'),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                TextInput::make('fiscal_year_start_month')
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('is_default')
                    ->required(),
            ]);
    }
}
