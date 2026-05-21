<?php

namespace App\Filament\Resources\HrLetterTemplates;

use App\Filament\Resources\HrLetterTemplates\Pages\CreateHrLetterTemplate;
use App\Filament\Resources\HrLetterTemplates\Pages\EditHrLetterTemplate;
use App\Filament\Resources\HrLetterTemplates\Pages\ListHrLetterTemplates;
use App\Filament\Resources\HrLetterTemplates\Schemas\HrLetterTemplateForm;
use App\Filament\Resources\HrLetterTemplates\Tables\HrLetterTemplatesTable;
use App\Models\HrLetterTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HrLetterTemplateResource extends Resource
{
    protected static ?string $model = HrLetterTemplate::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;
    protected static ?string $navigationLabel = 'Letter Templates';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return HrLetterTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HrLetterTemplatesTable::configure($table);
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
            'index' => ListHrLetterTemplates::route('/'),
            'create' => CreateHrLetterTemplate::route('/create'),
            'edit' => EditHrLetterTemplate::route('/{record}/edit'),
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
