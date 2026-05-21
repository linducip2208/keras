<?php

namespace App\Filament\Resources\Holidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('country_code')
                    ->required()
                    ->default('ID'),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options(['national' => 'National', 'religious' => 'Religious', 'cuti_bersama' => 'Cuti bersama'])
                    ->required(),
                Select::make('religion')
                    ->options([
            'islam' => 'Islam',
            'christian' => 'Christian',
            'catholic' => 'Catholic',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'confucian' => 'Confucian',
        ]),
            ]);
    }
}
