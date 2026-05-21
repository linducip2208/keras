<?php

namespace App\Filament\Resources\LeaveTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LeaveTypeForm
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
                TextInput::make('days_per_year')
                    ->numeric(),
                Toggle::make('is_paid')
                    ->required(),
                Toggle::make('requires_attachment')
                    ->required(),
                Toggle::make('requires_balance')
                    ->required(),
                Select::make('gender_specific')
                    ->options(['M' => 'M', 'F' => 'F']),
                TextInput::make('max_consecutive_days')
                    ->numeric(),
                TextInput::make('min_notice_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('can_be_half_day')
                    ->required(),
                Toggle::make('can_carry_over')
                    ->required(),
                TextInput::make('carry_over_max_days')
                    ->numeric(),
                TextInput::make('carry_over_expires_in_months')
                    ->numeric(),
                TextInput::make('deducts_from'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
