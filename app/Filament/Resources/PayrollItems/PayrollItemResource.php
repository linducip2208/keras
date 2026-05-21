<?php

namespace App\Filament\Resources\PayrollItems;

use App\Filament\Resources\PayrollItems\Pages\CreatePayrollItem;
use App\Filament\Resources\PayrollItems\Pages\EditPayrollItem;
use App\Filament\Resources\PayrollItems\Pages\ListPayrollItems;
use App\Filament\Resources\PayrollItems\Schemas\PayrollItemForm;
use App\Filament\Resources\PayrollItems\Tables\PayrollItemsTable;
use App\Models\PayrollItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PayrollItemResource extends Resource
{
    protected static ?string $model = PayrollItem::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static ?string $navigationLabel = 'Item Gaji';
    protected static \UnitEnum|string|null $navigationGroup = '💰  Payroll';
    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return PayrollItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollItemsTable::configure($table);
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
            'index' => ListPayrollItems::route('/'),
            'create' => CreatePayrollItem::route('/create'),
            'edit' => EditPayrollItem::route('/{record}/edit'),
        ];
    }
}
