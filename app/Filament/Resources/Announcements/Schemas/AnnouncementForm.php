<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('attachment_paths'),
                TextInput::make('created_by_user_id')
                    ->numeric(),
                TextInput::make('segment'),
                DateTimePicker::make('publish_at'),
                DateTimePicker::make('expire_at'),
                Toggle::make('is_pinned')
                    ->required(),
                TextInput::make('priority')
                    ->required()
                    ->numeric()
                    ->default(5),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'expired' => 'Expired'])
                    ->default('draft')
                    ->required(),
            ]);
    }
}
