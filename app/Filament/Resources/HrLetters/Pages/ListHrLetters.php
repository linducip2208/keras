<?php

namespace App\Filament\Resources\HrLetters\Pages;

use App\Filament\Resources\HrLetters\HrLetterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHrLetters extends ListRecords
{
    protected static string $resource = HrLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
