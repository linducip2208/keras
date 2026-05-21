<?php

namespace App\Filament\Resources\Payslips\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PayslipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('payroll_item_id')
                    ->required()
                    ->numeric(),
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('slip_number')
                    ->required(),
                DatePicker::make('slip_date')
                    ->required(),
                TextInput::make('pdf_path'),
                DateTimePicker::make('sent_email_at'),
                DateTimePicker::make('sent_whatsapp_at'),
                DateTimePicker::make('opened_at'),
                DateTimePicker::make('downloaded_at'),
                TextInput::make('password_hint')
                    ->password(),
            ]);
    }
}
