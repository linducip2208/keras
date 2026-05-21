<?php

namespace App\Filament\Resources\DisbursementRuns\Pages;

use App\Filament\Resources\DisbursementRuns\DisbursementRunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisbursementRuns extends ListRecords
{
    protected static string $resource = DisbursementRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
