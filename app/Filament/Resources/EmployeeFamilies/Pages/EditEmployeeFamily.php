<?php

namespace App\Filament\Resources\EmployeeFamilies\Pages;

use App\Filament\Resources\EmployeeFamilies\EmployeeFamilyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeFamily extends EditRecord
{
    protected static string $resource = EmployeeFamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
