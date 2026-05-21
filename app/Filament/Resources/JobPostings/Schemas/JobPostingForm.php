<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class JobPostingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('department_id')
                    ->numeric(),
                TextInput::make('position_id')
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('requirements')
                    ->columnSpanFull(),
                TextInput::make('location'),
                TextInput::make('employment_type'),
                TextInput::make('salary_min')
                    ->numeric(),
                TextInput::make('salary_max')
                    ->numeric(),
                TextInput::make('salary_currency')
                    ->required()
                    ->default('IDR'),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed', 'filled' => 'Filled'])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('closed_at'),
                TextInput::make('posted_by_user_id')
                    ->numeric(),
                TextInput::make('external_links'),
            ]);
    }
}
