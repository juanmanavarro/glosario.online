<?php

namespace App\Filament\Resources\TermResource\Pages;

use App\Filament\Resources\TermResource;
use App\Filament\Resources\TermResource\Pages\Concerns\ManagesSpanishVersion;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTerm extends EditRecord
{
    use ManagesSpanishVersion;

    protected static string $resource = TermResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);
        $data['es_version'] = $this->getSpanishVersionFormData($this->record);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $esVersionData = $this->pullSpanishVersionData($data);
        $data = $this->ensureTermSlug($data, $esVersionData);
        $data = $this->ensurePublishedAtWhenPublished($data);

        $record = parent::handleRecordUpdate($record, $data);

        /** @var \App\Models\Term $record */
        $this->syncSpanishWorkingDraftVersion($record, $esVersionData);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
