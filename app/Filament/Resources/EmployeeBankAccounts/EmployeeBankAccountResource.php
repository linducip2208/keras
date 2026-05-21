<?php

namespace App\Filament\Resources\EmployeeBankAccounts;

use App\Filament\Resources\EmployeeBankAccounts\Pages\CreateEmployeeBankAccount;
use App\Filament\Resources\EmployeeBankAccounts\Pages\EditEmployeeBankAccount;
use App\Filament\Resources\EmployeeBankAccounts\Pages\ListEmployeeBankAccounts;
use App\Filament\Resources\EmployeeBankAccounts\Schemas\EmployeeBankAccountForm;
use App\Filament\Resources\EmployeeBankAccounts\Tables\EmployeeBankAccountsTable;
use App\Models\EmployeeBankAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmployeeBankAccountResource extends Resource
{
    protected static ?string $model = EmployeeBankAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EmployeeBankAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeBankAccountsTable::configure($table);
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
            'index' => ListEmployeeBankAccounts::route('/'),
            'create' => CreateEmployeeBankAccount::route('/create'),
            'edit' => EditEmployeeBankAccount::route('/{record}/edit'),
        ];
    }
}
