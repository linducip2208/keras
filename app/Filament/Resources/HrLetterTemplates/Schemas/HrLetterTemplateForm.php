<?php

namespace App\Filament\Resources\HrLetterTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HrLetterTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
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
                TextInput::make('name')
                    ->required(),
                Textarea::make('body_template')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('variables'),
                TextInput::make('default_signer_role'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
