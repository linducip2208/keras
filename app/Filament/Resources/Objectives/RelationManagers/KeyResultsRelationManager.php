<?php

namespace App\Filament\Resources\Objectives\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KeyResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'keyResults';
    protected static ?string $title = 'Key Results';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Key Result')->required()->columnSpanFull(),
            Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            Select::make('metric_type')->label('Tipe Metrik')
                ->options(['number' => 'Angka', 'percentage' => 'Persentase', 'currency' => 'Rupiah', 'boolean' => 'Ya/Tidak'])
                ->default('number')->required(),
            TextInput::make('start_value')->label('Nilai Awal')->numeric()->default(0)->required(),
            TextInput::make('target_value')->label('Target')->numeric()->required(),
            TextInput::make('current_value')->label('Capaian Saat Ini')->numeric()->default(0)->required(),
            TextInput::make('unit')->label('Satuan')->placeholder('% / Rp / pcs'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Key Result')->searchable(),
                TextColumn::make('current_value')->label('Capaian')->numeric(),
                TextColumn::make('target_value')->label('Target')->numeric(),
                TextColumn::make('progress_percent')->label('Progress %')->numeric()->suffix('%'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
