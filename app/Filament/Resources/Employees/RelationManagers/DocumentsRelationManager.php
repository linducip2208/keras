<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Dokumen';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama Dokumen')->required()
                ->placeholder('e.g., KTP, NPWP, Ijazah S1, Sertifikat K3'),
            Select::make('category_id')->label('Kategori')
                ->relationship('category', 'name')->searchable()->preload(),
            FileUpload::make('file_path')->label('File')
                ->directory('employees/documents')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->maxSize(10240)
                ->required()
                ->columnSpanFull(),
            DatePicker::make('expires_at')->label('Berlaku Sampai (opsional)')->native(false),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('category.name')->label('Kategori')->badge(),
                TextColumn::make('expires_at')->label('Expire')->date('d M Y')->placeholder('—'),
                TextColumn::make('uploaded_by_user_id')->label('Uploaded By')->toggleable(),
                TextColumn::make('created_at')->label('Diunggah')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
