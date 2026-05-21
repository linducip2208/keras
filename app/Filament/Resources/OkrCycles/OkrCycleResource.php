<?php

namespace App\Filament\Resources\OkrCycles;

use App\Filament\Resources\OkrCycles\Pages\CreateOkrCycle;
use App\Filament\Resources\OkrCycles\Pages\EditOkrCycle;
use App\Filament\Resources\OkrCycles\Pages\ListOkrCycles;
use App\Filament\Resources\OkrCycles\Schemas\OkrCycleForm;
use App\Filament\Resources\OkrCycles\Tables\OkrCyclesTable;
use App\Models\OkrCycle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OkrCycleResource extends Resource
{
    protected static ?string $model = OkrCycle::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;
    protected static ?string $navigationLabel = 'OKR Cycles';
    protected static \UnitEnum|string|null $navigationGroup = 'Talent';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return OkrCycleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OkrCyclesTable::configure($table);
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
            'index' => ListOkrCycles::route('/'),
            'create' => CreateOkrCycle::route('/create'),
            'edit' => EditOkrCycle::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
