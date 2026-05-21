<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'bankAccounts';
    protected static ?string $title = 'Rekening Bank';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('bank_name')->label('Bank')
                ->options([
                    'BCA' => 'BCA', 'Mandiri' => 'Mandiri', 'BNI' => 'BNI', 'BRI' => 'BRI',
                    'CIMB Niaga' => 'CIMB Niaga', 'Permata' => 'Permata', 'Danamon' => 'Danamon',
                    'BTN' => 'BTN', 'Maybank' => 'Maybank', 'Mega' => 'Mega',
                    'DBS' => 'DBS', 'Jago' => 'Jago', 'SeaBank' => 'SeaBank', 'Allo' => 'Allo',
                ])->searchable()->required()->columnSpan(1),
            TextInput::make('account_number')->label('Nomor Rekening')->password()->revealable()->required()->columnSpan(1),
            TextInput::make('account_holder_name')->label('Nama Pemegang Rekening')->required()->columnSpan(1),
            TextInput::make('allocation_percentage')->label('% Alokasi')->numeric()->suffix('%')->default(100)->columnSpan(1),
            Toggle::make('is_primary')->label('Rekening Utama')->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')->label('Bank')->badge(),
                TextColumn::make('account_number')->label('Nomor')
                    ->formatStateUsing(fn ($state) => str_repeat('•', max(0, strlen($state) - 4)) . substr($state, -4)),
                TextColumn::make('account_holder_name')->label('Pemegang'),
                TextColumn::make('allocation_percentage')->label('Alokasi')->suffix('%'),
                IconColumn::make('is_primary')->label('Primary')->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
