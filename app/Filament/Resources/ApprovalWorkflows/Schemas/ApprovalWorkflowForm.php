<?php

namespace App\Filament\Resources\ApprovalWorkflows\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApprovalWorkflowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('applies_to')
                    ->options([
            'leave' => 'Leave',
            'overtime' => 'Overtime',
            'reimburse' => 'Reimburse',
            'travel' => 'Travel',
            'loan' => 'Loan',
            'attendance' => 'Attendance',
            'custom' => 'Custom',
        ])
                    ->required(),
                TextInput::make('conditions'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('priority')
                    ->required()
                    ->numeric()
                    ->default(10),
            ]);
    }
}
