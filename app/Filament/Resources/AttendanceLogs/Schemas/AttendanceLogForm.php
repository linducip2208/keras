<?php

namespace App\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttendanceLogForm
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
                TextInput::make('shift_id')
                    ->numeric(),
                DateTimePicker::make('check_in_at'),
                TextInput::make('check_in_latitude')
                    ->numeric(),
                TextInput::make('check_in_longitude')
                    ->numeric(),
                Textarea::make('check_in_address')
                    ->columnSpanFull(),
                TextInput::make('check_in_photo_path'),
                Select::make('check_in_method')
                    ->options([
            'gps' => 'Gps',
            'fingerprint' => 'Fingerprint',
            'qr' => 'Qr',
            'face' => 'Face',
            'manual' => 'Manual',
        ]),
                TextInput::make('check_in_branch_id')
                    ->numeric(),
                Toggle::make('check_in_is_late')
                    ->required(),
                TextInput::make('check_in_late_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('check_in_is_mock_gps')
                    ->required(),
                DateTimePicker::make('check_out_at'),
                TextInput::make('check_out_latitude')
                    ->numeric(),
                TextInput::make('check_out_longitude')
                    ->numeric(),
                Textarea::make('check_out_address')
                    ->columnSpanFull(),
                TextInput::make('check_out_photo_path'),
                Select::make('check_out_method')
                    ->options([
            'gps' => 'Gps',
            'fingerprint' => 'Fingerprint',
            'qr' => 'Qr',
            'face' => 'Face',
            'manual' => 'Manual',
        ]),
                TextInput::make('check_out_branch_id')
                    ->numeric(),
                Toggle::make('check_out_is_early')
                    ->required(),
                TextInput::make('check_out_early_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('check_out_is_mock_gps')
                    ->required(),
                TextInput::make('worked_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('overtime_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options([
            'present' => 'Present',
            'absent' => 'Absent',
            'leave' => 'Leave',
            'half_day' => 'Half day',
            'holiday' => 'Holiday',
            'weekend' => 'Weekend',
        ])
                    ->default('absent')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
