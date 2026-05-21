<?php

namespace App\Filament\Resources\DisbursementRuns\Pages;

use App\Filament\Resources\DisbursementRuns\DisbursementRunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDisbursementRun extends EditRecord
{
    protected static string $resource = DisbursementRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
