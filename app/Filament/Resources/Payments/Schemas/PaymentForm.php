<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('invoice_id')
                    ->numeric(),
                TextInput::make('adapter_config_id')
                    ->numeric(),
                TextInput::make('reference')
                    ->required(),
                TextInput::make('external_reference_id'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                TextInput::make('payment_method')
                    ->required(),
                TextInput::make('payment_channel'),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'expired' => 'Expired',
            'refunded' => 'Refunded',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('payment_url')
                    ->url(),
                Textarea::make('qr_string')
                    ->columnSpanFull(),
                TextInput::make('va_number'),
                DateTimePicker::make('expires_at'),
                DateTimePicker::make('paid_at'),
                TextInput::make('metadata'),
                TextInput::make('webhook_payload'),
            ]);
    }
}
