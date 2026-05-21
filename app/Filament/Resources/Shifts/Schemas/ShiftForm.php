<?php

namespace App\Filament\Resources\Shifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShiftForm
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
                TimePicker::make('start_time')
                    ->required(),
                TimePicker::make('end_time')
                    ->required(),
                TextInput::make('break_minutes')
                    ->required()
                    ->numeric()
                    ->default(60),
                TextInput::make('working_hours')
                    ->required()
                    ->numeric()
                    ->default(8.0),
                Toggle::make('crosses_midnight')
                    ->required(),
                TextInput::make('allowed_late_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('allowed_early_leave_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('color')
                    ->required()
                    ->default('#3b82f6'),
                Toggle::make('is_off_day')
                    ->required(),
            ]);
    }
}
