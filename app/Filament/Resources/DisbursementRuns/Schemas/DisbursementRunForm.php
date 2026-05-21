<?php

namespace App\Filament\Resources\DisbursementRuns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DisbursementRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('payroll_run_id')
                    ->numeric(),
                Select::make('disbursement_method')
                    ->options(['batch_file' => 'Batch file', 'instant' => 'Instant', 'manual' => 'Manual'])
                    ->required(),
                TextInput::make('adapter_slug'),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'partially_completed' => 'Partially completed',
            'failed' => 'Failed',
        ])
                    ->default('draft')
                    ->required(),
                TextInput::make('file_path'),
                DateTimePicker::make('executed_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
