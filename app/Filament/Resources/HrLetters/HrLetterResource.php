<?php

namespace App\Filament\Resources\HrLetters;

use App\Filament\Resources\HrLetters\Pages\CreateHrLetter;
use App\Filament\Resources\HrLetters\Pages\EditHrLetter;
use App\Filament\Resources\HrLetters\Pages\ListHrLetters;
use App\Filament\Resources\HrLetters\Schemas\HrLetterForm;
use App\Filament\Resources\HrLetters\Tables\HrLettersTable;
use App\Models\HrLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HrLetterResource extends Resource
{
    protected static ?string $model = HrLetter::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = 'HR Letters';
    protected static \UnitEnum|string|null $navigationGroup = 'Core HR';
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return HrLetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HrLettersTable::configure($table);
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
            'index' => ListHrLetters::route('/'),
            'create' => CreateHrLetter::route('/create'),
            'edit' => EditHrLetter::route('/{record}/edit'),
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
