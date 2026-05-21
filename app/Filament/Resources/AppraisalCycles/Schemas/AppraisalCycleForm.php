<?php

namespace App\Filament\Resources\AppraisalCycles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AppraisalCycleForm
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
                Select::make('cycle_type')
                    ->options(['annual' => 'Annual', 'semester' => 'Semester', 'quarterly' => 'Quarterly'])
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                DatePicker::make('self_review_start'),
                DatePicker::make('self_review_end'),
                DatePicker::make('peer_review_start'),
                DatePicker::make('peer_review_end'),
                DatePicker::make('manager_review_start'),
                DatePicker::make('manager_review_end'),
                DatePicker::make('calibration_date'),
                DatePicker::make('finalization_date'),
                Select::make('status')
                    ->options([
            'planning' => 'Planning',
            'active' => 'Active',
            'review' => 'Review',
            'calibration' => 'Calibration',
            'completed' => 'Completed',
        ])
                    ->default('planning')
                    ->required(),
            ]);
    }
}
