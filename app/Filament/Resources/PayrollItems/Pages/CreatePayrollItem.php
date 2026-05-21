<?php

namespace App\Filament\Resources\PayrollItems\Pages;

use App\Filament\Resources\PayrollItems\PayrollItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayrollItem extends CreateRecord
{
    protected static string $resource = PayrollItemResource::class;
}
