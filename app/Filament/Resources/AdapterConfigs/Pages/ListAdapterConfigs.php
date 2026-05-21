<?php

namespace App\Filament\Resources\AdapterConfigs\Pages;

use App\Filament\Resources\AdapterConfigs\AdapterConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdapterConfigs extends ListRecords
{
    protected static string $resource = AdapterConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
