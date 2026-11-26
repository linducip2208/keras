<?php

namespace App\Filament\Resources\TaxCalculations\Pages;

use App\Filament\Resources\TaxCalculations\TaxCalculationResource;
use Filament\Resources\Pages\ListRecords;

class ListTaxCalculations extends ListRecords
{
    protected static string $resource = TaxCalculationResource::class;
}
