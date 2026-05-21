<?php

namespace App\Filament\Resources\ReimbursementCategories;

use App\Filament\Resources\ReimbursementCategories\Pages\CreateReimbursementCategory;
use App\Filament\Resources\ReimbursementCategories\Pages\EditReimbursementCategory;
use App\Filament\Resources\ReimbursementCategories\Pages\ListReimbursementCategories;
use App\Filament\Resources\ReimbursementCategories\Schemas\ReimbursementCategoryForm;
use App\Filament\Resources\ReimbursementCategories\Tables\ReimbursementCategoriesTable;
use App\Models\ReimbursementCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReimbursementCategoryResource extends Resource
{
    protected static ?string $model = ReimbursementCategory::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static ?string $navigationLabel = 'Kategori Reimburse';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return ReimbursementCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReimbursementCategoriesTable::configure($table);
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
            'index' => ListReimbursementCategories::route('/'),
            'create' => CreateReimbursementCategory::route('/create'),
            'edit' => EditReimbursementCategory::route('/{record}/edit'),
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
