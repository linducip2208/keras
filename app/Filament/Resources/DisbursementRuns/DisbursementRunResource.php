<?php

namespace App\Filament\Resources\DisbursementRuns;

use App\Filament\Resources\DisbursementRuns\Pages\CreateDisbursementRun;
use App\Filament\Resources\DisbursementRuns\Pages\EditDisbursementRun;
use App\Filament\Resources\DisbursementRuns\Pages\ListDisbursementRuns;
use App\Filament\Resources\DisbursementRuns\Schemas\DisbursementRunForm;
use App\Filament\Resources\DisbursementRuns\Tables\DisbursementRunsTable;
use App\Models\DisbursementRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DisbursementRunResource extends Resource
{
    protected static ?string $model = DisbursementRun::class;

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;
    protected static ?string $navigationLabel = 'Disbursement Runs';
    protected static \UnitEnum|string|null $navigationGroup = 'Payroll';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return DisbursementRunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisbursementRunsTable::configure($table);
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
            'index' => ListDisbursementRuns::route('/'),
            'create' => CreateDisbursementRun::route('/create'),
            'edit' => EditDisbursementRun::route('/{record}/edit'),
        ];
    }
}
