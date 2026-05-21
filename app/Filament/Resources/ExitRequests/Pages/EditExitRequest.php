<?php

namespace App\Filament\Resources\ExitRequests\Pages;

use App\Filament\Resources\ExitRequests\ExitRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditExitRequest extends EditRecord
{
    protected static string $resource = ExitRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
