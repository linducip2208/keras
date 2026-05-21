<?php

namespace App\Filament\Resources\HrLetterTemplates\Pages;

use App\Filament\Resources\HrLetterTemplates\HrLetterTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHrLetterTemplate extends EditRecord
{
    protected static string $resource = HrLetterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
