<?php

namespace App\Filament\Resources\ReimbursementRequests\Pages;

use App\Filament\Resources\ReimbursementRequests\ReimbursementRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditReimbursementRequest extends EditRecord
{
    protected static string $resource = ReimbursementRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
