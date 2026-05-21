<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('brand'),
                TextInput::make('model'),
                TextInput::make('serial_number'),
                TextInput::make('color'),
                DatePicker::make('purchase_date'),
                TextInput::make('purchase_amount')
                    ->numeric(),
                TextInput::make('supplier'),
                DatePicker::make('warranty_until'),
                TextInput::make('current_value')
                    ->numeric(),
                Select::make('condition')
                    ->options([
            'new' => 'New',
            'good' => 'Good',
            'fair' => 'Fair',
            'damaged' => 'Damaged',
            'written_off' => 'Written off',
        ])
                    ->default('good')
                    ->required(),
                TextInput::make('location'),
                Select::make('status')
                    ->options([
            'in_stock' => 'In stock',
            'assigned' => 'Assigned',
            'maintenance' => 'Maintenance',
            'disposed' => 'Disposed',
            'lost' => 'Lost',
        ])
                    ->default('in_stock')
                    ->required(),
                TextInput::make('photo_path'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
