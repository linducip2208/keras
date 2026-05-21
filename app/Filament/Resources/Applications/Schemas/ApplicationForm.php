<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('candidate_id')
                    ->required()
                    ->numeric(),
                TextInput::make('job_posting_id')
                    ->required()
                    ->numeric(),
                TextInput::make('current_stage_id')
                    ->numeric(),
                TextInput::make('ai_match_score')
                    ->numeric(),
                Textarea::make('cover_letter')
                    ->columnSpanFull(),
                TextInput::make('salary_expectation')
                    ->numeric(),
                TextInput::make('notice_period_days')
                    ->numeric(),
                Select::make('status')
                    ->options([
            'active' => 'Active',
            'hired' => 'Hired',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            'on_hold' => 'On hold',
        ])
                    ->default('active')
                    ->required(),
                TextInput::make('hired_employee_id')
                    ->numeric(),
                Textarea::make('rejected_reason')
                    ->columnSpanFull(),
                DateTimePicker::make('applied_at'),
            ]);
    }
}
