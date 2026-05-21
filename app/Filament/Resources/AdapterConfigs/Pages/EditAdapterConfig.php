<?php

namespace App\Filament\Resources\AdapterConfigs\Pages;

use App\Filament\Resources\AdapterConfigs\AdapterConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdapterConfig extends EditRecord
{
    protected static string $resource = AdapterConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
