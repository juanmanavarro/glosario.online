<?php

namespace App\Filament\Resources\TermVersionResource\Pages;

use App\Filament\Resources\TermVersionResource;
use Filament\Resources\Pages\ListRecords;

class ListTermVersions extends ListRecords
{
    protected static string $resource = TermVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
