<?php

namespace App\Filament\Resources\ApprovalRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApprovalRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('workflow_id')
                    ->required()
                    ->numeric(),
                TextInput::make('requestable_type')
                    ->required(),
                TextInput::make('requestable_id')
                    ->required()
                    ->numeric(),
                TextInput::make('requester_employee_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'canceled' => 'Canceled',
            'escalated' => 'Escalated',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('current_step_order')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('total_steps')
                    ->required()
                    ->numeric()
                    ->default(1),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
