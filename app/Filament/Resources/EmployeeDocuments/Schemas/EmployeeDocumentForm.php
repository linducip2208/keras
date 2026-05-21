<?php

namespace App\Filament\Resources\EmployeeDocuments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('category_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('file_size')
                    ->numeric(),
                TextInput::make('mime_type'),
                DatePicker::make('expires_at'),
                TextInput::make('uploaded_by_user_id')
                    ->numeric(),
                TextInput::make('metadata'),
            ]);
    }
}
