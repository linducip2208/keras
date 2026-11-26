<?php

namespace App\Filament\Resources\ApprovalWorkflows\RelationManagers;

use App\Models\Employee;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';
    protected static ?string $title = 'Tahapan Approval';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order')->label('Urutan')->numeric()->required()->minValue(1),
            TextInput::make('name')->label('Nama Tahap')->required()->columnSpanFull(),
            Select::make('approver_type')->label('Approver')
                ->options([
                    'manager' => 'Atasan Langsung',
                    'manager_of_manager' => 'Atasan dari Atasan',
                    'employee' => 'Karyawan Spesifik',
                    'hr' => 'HR',
                    'finance' => 'Finance',
                    'custom' => 'Custom (ID)',
                ])->required()->live(),
            Select::make('approver_id')->label('Karyawan / ID')
                ->options(fn () => Employee::where('tenant_id', auth()->user()->tenant_id)->pluck('full_name', 'id'))
                ->searchable()->visible(fn ($get) => $get('approver_type') === 'employee'),
            Select::make('approval_mode')->label('Mode')
                ->options(['single' => 'Single', 'all' => 'Semua Harus Approve'])
                ->default('single')->required(),
            TextInput::make('timeout_hours')->label('Timeout (jam)')->numeric()->placeholder('Kosong = tanpa timeout'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')->label('#')->sortable(),
                TextColumn::make('name')->label('Tahap')->searchable(),
                TextColumn::make('approver_type')->label('Approver')->badge(),
                TextColumn::make('approval_mode')->label('Mode')->badge(),
                TextColumn::make('timeout_hours')->label('Timeout (jam)')->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
