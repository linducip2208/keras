<?php

namespace App\Filament\Resources\AssetCategories;

use App\Filament\Resources\AssetCategories\Pages\CreateAssetCategory;
use App\Filament\Resources\AssetCategories\Pages\EditAssetCategory;
use App\Filament\Resources\AssetCategories\Pages\ListAssetCategories;
use App\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Filament\Resources\AssetCategories\Tables\AssetCategoriesTable;
use App\Models\AssetCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?string $navigationLabel = 'Kategori Aset';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return AssetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetCategoriesTable::configure($table);
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
            'index' => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'edit' => EditAssetCategory::route('/{record}/edit'),
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
