<?php

namespace App\Filament\Resources\AdapterConfigs\Pages;

use App\Filament\Resources\AdapterConfigs\AdapterConfigResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdapterConfig extends CreateRecord
{
    protected static string $resource = AdapterConfigResource::class;
}
