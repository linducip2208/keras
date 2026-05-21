<?php

namespace App\Filament\Resources\Candidates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CandidateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('linkedin_url')
                    ->url(),
                TextInput::make('portfolio_url')
                    ->url(),
                TextInput::make('location'),
                TextInput::make('current_position'),
                TextInput::make('years_experience')
                    ->numeric(),
                TextInput::make('cv_path'),
                TextInput::make('photo_path'),
                Textarea::make('ai_summary')
                    ->columnSpanFull(),
                TextInput::make('ai_score')
                    ->numeric(),
                TextInput::make('talent_pool_tags'),
                TextInput::make('source'),
                TextInput::make('referred_by_employee_id')
                    ->numeric(),
            ]);
    }
}
