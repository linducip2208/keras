<?php

namespace App\Filament\Resources\PayrollItems\Pages;

use App\Filament\Resources\PayrollItems\PayrollItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayrollItems extends ListRecords
{
    protected static string $resource = PayrollItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
