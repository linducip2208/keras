<?php

namespace App\Filament\Resources\ReimbursementRequests\Pages;

use App\Filament\Resources\ReimbursementRequests\ReimbursementRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursementRequest extends CreateRecord
{
    protected static string $resource = ReimbursementRequestResource::class;
}
