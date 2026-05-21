<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FamiliesRelationManager extends RelationManager
{
    protected static string $relationship = 'families';
    protected static ?string $title = 'Keluarga';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('relation')->label('Hubungan')
                ->options(['spouse'=>'Suami/Istri','child'=>'Anak','parent'=>'Orang Tua','sibling'=>'Saudara'])
                ->required(),
            TextInput::make('name')->label('Nama')->required(),
            Select::make('gender')->label('Jenis Kelamin')->options(['M'=>'Laki-laki','F'=>'Perempuan']),
            DatePicker::make('date_of_birth')->label('Tanggal Lahir')->native(false),
            TextInput::make('nik')->label('NIK')->password()->revealable()->maxLength(16),
            Toggle::make('is_bpjs_dependent')->label('Tanggungan BPJS Kesehatan'),
            Toggle::make('is_ptkp_dependent')->label('Tanggungan PTKP'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('relation')->label('Hubungan')->badge(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('gender')->label('Sex')->badge(),
                TextColumn::make('date_of_birth')->label('TGL Lahir')->date('d M Y'),
                IconColumn::make('is_bpjs_dependent')->label('BPJS')->boolean(),
                IconColumn::make('is_ptkp_dependent')->label('PTKP')->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
