<?php

namespace App\Filament\Resources\ExitRequests;

use App\Filament\Resources\ExitRequests\Pages\CreateExitRequest;
use App\Filament\Resources\ExitRequests\Pages\EditExitRequest;
use App\Filament\Resources\ExitRequests\Pages\ListExitRequests;
use App\Filament\Resources\ExitRequests\Schemas\ExitRequestForm;
use App\Filament\Resources\ExitRequests\Tables\ExitRequestsTable;
use App\Models\ExitRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExitRequestResource extends Resource
{
    protected static ?string $model = ExitRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ExitRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExitRequestsTable::configure($table);
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
            'index' => ListExitRequests::route('/'),
            'create' => CreateExitRequest::route('/create'),
            'edit' => EditExitRequest::route('/{record}/edit'),
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
