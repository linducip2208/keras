<?php

namespace App\Filament\Resources\ExitRequests\Pages;

use App\Filament\Resources\ExitRequests\ExitRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExitRequests extends ListRecords
{
    protected static string $resource = ExitRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
