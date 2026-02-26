<?php

namespace App\Filament\Resources\EditorialLogResource\Pages;

use App\Filament\Resources\EditorialLogResource;
use Filament\Resources\Pages\ListRecords;

class ListEditorialLogs extends ListRecords
{
    protected static string $resource = EditorialLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
