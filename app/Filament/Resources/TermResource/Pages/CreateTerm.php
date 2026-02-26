<?php

namespace App\Filament\Resources\TermResource\Pages;

use App\Filament\Resources\TermResource;
use App\Filament\Resources\TermResource\Pages\Concerns\ManagesSpanishVersion;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTerm extends CreateRecord
{
    use ManagesSpanishVersion;

    protected static string $resource = TermResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $esVersionData = $this->pullSpanishVersionData($data);
        $data = $this->ensureTermSlug($data, $esVersionData);
        $data = $this->ensurePublishedAtWhenPublished($data);

        /** @var \App\Models\Term $record */
        $record = parent::handleRecordCreation($data);
        $this->syncSpanishWorkingDraftVersion($record, $esVersionData);

        return $record;
    }
}
