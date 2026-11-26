<?php

namespace App\Filament\Resources\TaxCalculations;

use App\Filament\Resources\TaxCalculations\Pages\ListTaxCalculations;
use App\Filament\Resources\TaxCalculations\Pages\ViewTaxCalculation;
use App\Filament\Resources\TaxCalculations\Tables\TaxCalculationsTable;
use App\Models\TaxCalculation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaxCalculationResource extends Resource
{
    protected static ?string $model = TaxCalculation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static ?string $navigationLabel = 'Riwayat PPh 21';
    protected static \UnitEnum|string|null $navigationGroup = '💰  Payroll';
    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return TaxCalculationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxCalculations::route('/'),
            'view' => ViewTaxCalculation::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery();
    }
}
