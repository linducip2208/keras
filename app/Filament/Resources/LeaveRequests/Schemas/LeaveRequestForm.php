<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LeaveRequestForm
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
                TextInput::make('leave_type_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                TextInput::make('days_count')
                    ->required()
                    ->numeric(),
                Toggle::make('is_half_day')
                    ->required(),
                Select::make('half_day_period')
                    ->options(['am' => 'Am', 'pm' => 'Pm']),
                Textarea::make('reason')
                    ->columnSpanFull(),
                TextInput::make('attachment_path'),
                TextInput::make('contact_during_leave'),
                TextInput::make('delegate_employee_id')
                    ->numeric(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'canceled' => 'Canceled',
        ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('applied_at'),
                DateTimePicker::make('canceled_at'),
            ]);
    }
}
