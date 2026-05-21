<?php

namespace App\Filament\Resources\OvertimeRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OvertimeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('planned_start'),
                TimePicker::make('planned_end'),
                DateTimePicker::make('actual_start'),
                DateTimePicker::make('actual_end'),
                TextInput::make('planned_hours')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('actual_hours')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('reason')
                    ->columnSpanFull(),
                TextInput::make('attachment_path'),
                Select::make('overtime_type')
                    ->options(['workday' => 'Workday', 'weekend' => 'Weekend', 'holiday' => 'Holiday'])
                    ->default('workday')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('amount_calculated')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Toggle::make('use_compensatory_leave')
                    ->required(),
            ]);
    }
}
