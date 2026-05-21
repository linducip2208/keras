<?php

namespace App\Filament\Resources\PayrollComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PayrollComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options(['earning' => 'Earning', 'deduction' => 'Deduction', 'benefit' => 'Benefit'])
                    ->required(),
                Select::make('calculation')
                    ->options([
            'fixed' => 'Fixed',
            'formula' => 'Formula',
            'attendance_based' => 'Attendance based',
            'manual' => 'Manual',
        ])
                    ->default('fixed')
                    ->required(),
                Textarea::make('formula')
                    ->columnSpanFull(),
                TextInput::make('default_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Toggle::make('is_taxable')
                    ->required(),
                Toggle::make('is_bpjs_basis')
                    ->required(),
                Toggle::make('is_thr_basis')
                    ->required(),
                Toggle::make('is_pension_basis')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
