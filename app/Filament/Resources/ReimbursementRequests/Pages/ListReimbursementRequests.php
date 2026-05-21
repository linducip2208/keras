<?php

namespace App\Filament\Resources\ReimbursementRequests\Pages;

use App\Filament\Resources\ReimbursementRequests\ReimbursementRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReimbursementRequests extends ListRecords
{
    protected static string $resource = ReimbursementRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
