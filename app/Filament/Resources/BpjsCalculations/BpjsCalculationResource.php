<?php

namespace App\Filament\Resources\BpjsCalculations;

use App\Filament\Resources\BpjsCalculations\Pages\ListBpjsCalculations;
use App\Filament\Resources\BpjsCalculations\Pages\ViewBpjsCalculation;
use App\Filament\Resources\BpjsCalculations\Tables\BpjsCalculationsTable;
use App\Models\BPJSCalculation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BpjsCalculationResource extends Resource
{
    protected static ?string $model = BPJSCalculation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;
    protected static ?string $navigationLabel = 'Riwayat BPJS';
    protected static \UnitEnum|string|null $navigationGroup = '💰  Payroll';
    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return BpjsCalculationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBpjsCalculations::route('/'),
            'view' => ViewBpjsCalculation::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
