<?php

namespace App\Filament\Resources\HrLetterTemplates\Pages;

use App\Filament\Resources\HrLetterTemplates\HrLetterTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHrLetterTemplates extends ListRecords
{
    protected static string $resource = HrLetterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
