<?php

namespace App\Filament\Resources\HrLetters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HrLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('employee_id')
                    ->numeric(),
                Select::make('letter_type')
                    ->options([
            'sk' => 'Sk',
            'paklaring' => 'Paklaring',
            'sp1' => 'Sp1',
            'sp2' => 'Sp2',
            'sp3' => 'Sp3',
            'spd' => 'Spd',
            'mutasi' => 'Mutasi',
            'promosi' => 'Promosi',
            'demosi' => 'Demosi',
            'referensi' => 'Referensi',
            'custom' => 'Custom',
        ])
                    ->required(),
                TextInput::make('template_id')
                    ->numeric(),
                TextInput::make('letter_no')
                    ->required(),
                DatePicker::make('letter_date')
                    ->required(),
                TextInput::make('variables_data'),
                Textarea::make('body_rendered')
                    ->columnSpanFull(),
                TextInput::make('file_path'),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'finalized' => 'Finalized',
            'signed' => 'Signed',
            'delivered' => 'Delivered',
            'archived' => 'Archived',
        ])
                    ->default('draft')
                    ->required(),
                TextInput::make('created_by_user_id')
                    ->numeric(),
                DateTimePicker::make('signed_at'),
                DateTimePicker::make('delivered_at'),
                TextInput::make('e_sign_provider'),
                TextInput::make('e_sign_reference_id'),
            ]);
    }
}
