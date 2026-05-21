<?php

namespace App\Filament\Resources\AdapterConfigs;

use App\Filament\Resources\AdapterConfigs\Pages\CreateAdapterConfig;
use App\Filament\Resources\AdapterConfigs\Pages\EditAdapterConfig;
use App\Filament\Resources\AdapterConfigs\Pages\ListAdapterConfigs;
use App\Filament\Resources\AdapterConfigs\Schemas\AdapterConfigForm;
use App\Filament\Resources\AdapterConfigs\Tables\AdapterConfigsTable;
use App\Models\AdapterConfig;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdapterConfigResource extends Resource
{
    protected static ?string $model = AdapterConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AdapterConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdapterConfigsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdapterConfigs::route('/'),
            'create' => CreateAdapterConfig::route('/create'),
            'edit' => EditAdapterConfig::route('/{record}/edit'),
        ];
    }
}
