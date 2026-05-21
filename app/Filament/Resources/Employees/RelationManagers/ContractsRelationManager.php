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
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';
    protected static ?string $title = 'Kontrak';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('contract_type')->label('Jenis')
                ->options([
                    'PKWT' => 'PKWT (Kontrak)',
                    'PKWTT' => 'PKWTT (Tetap)',
                    'probation' => 'Probation',
                    'intern' => 'Magang',
                    'outsource' => 'Outsource',
                    'freelance' => 'Freelance',
                    'daily' => 'Harian',
                ])->required()->columnSpan(1),
            TextInput::make('contract_no')->label('Nomor Kontrak')->required()->columnSpan(1),
            DatePicker::make('start_date')->label('Mulai')->required()->native(false),
            DatePicker::make('end_date')->label('Selesai')->native(false),
            DatePicker::make('probation_end_date')->label('Akhir Probation')->native(false),
            TextInput::make('salary_base')->label('Gaji Pokok')->numeric()->required()->prefix('Rp'),
            Select::make('status')->label('Status')
                ->options([
                    'draft' => 'Draft',
                    'active' => 'Aktif',
                    'expired' => 'Expired',
                    'terminated' => 'Terminated',
                    'renewed' => 'Renewed',
                ])->default('active')->required(),
            Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_no')->label('No.')->searchable(),
                TextColumn::make('contract_type')->label('Jenis')->badge(),
                TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                TextColumn::make('end_date')->label('Selesai')->date('d M Y')->placeholder('—'),
                TextColumn::make('salary_base')->label('Gaji')->money('IDR'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success', 'draft' => 'gray',
                        'expired', 'terminated' => 'danger',
                        'renewed' => 'info', default => 'gray',
                    }),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
