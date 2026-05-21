<?php

namespace App\Filament\Resources\ReimbursementRequests;

use App\Filament\Resources\ReimbursementRequests\Pages\CreateReimbursementRequest;
use App\Filament\Resources\ReimbursementRequests\Pages\EditReimbursementRequest;
use App\Filament\Resources\ReimbursementRequests\Pages\ListReimbursementRequests;
use App\Filament\Resources\ReimbursementRequests\Schemas\ReimbursementRequestForm;
use App\Filament\Resources\ReimbursementRequests\Tables\ReimbursementRequestsTable;
use App\Models\ReimbursementRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReimbursementRequestResource extends Resource
{
    protected static ?string $model = ReimbursementRequest::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static ?string $navigationLabel = 'Reimbursement';
    protected static \UnitEnum|string|null $navigationGroup = '💰  Payroll';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ReimbursementRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReimbursementRequestsTable::configure($table);
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
            'index' => ListReimbursementRequests::route('/'),
            'create' => CreateReimbursementRequest::route('/create'),
            'edit' => EditReimbursementRequest::route('/{record}/edit'),
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
