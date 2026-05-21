<?php

namespace App\Filament\Resources\HrLetters\Pages;

use App\Filament\Resources\HrLetters\HrLetterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHrLetter extends EditRecord
{
    protected static string $resource = HrLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
