<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('plan_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'trial' => 'Trial',
            'active' => 'Active',
            'past_due' => 'Past due',
            'canceled' => 'Canceled',
            'expired' => 'Expired',
            'suspended' => 'Suspended',
        ])
                    ->default('trial')
                    ->required(),
                DatePicker::make('current_period_start')
                    ->required(),
                DatePicker::make('current_period_end')
                    ->required(),
                DateTimePicker::make('canceled_at'),
                DateTimePicker::make('trial_ends_at'),
                Select::make('billing_cycle')
                    ->options(['monthly' => 'Monthly', 'annual' => 'Annual'])
                    ->default('monthly')
                    ->required(),
                TextInput::make('seats')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('external_subscription_id'),
                Toggle::make('auto_renew')
                    ->required(),
            ]);
    }
}
