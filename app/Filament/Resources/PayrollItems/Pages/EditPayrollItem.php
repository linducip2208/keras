<?php

namespace App\Filament\Resources\PayrollItems\Pages;

use App\Filament\Resources\PayrollItems\PayrollItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayrollItem extends EditRecord
{
    protected static string $resource = PayrollItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
