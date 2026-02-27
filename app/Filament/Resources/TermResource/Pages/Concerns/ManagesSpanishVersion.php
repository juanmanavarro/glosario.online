<?php

namespace App\Filament\Resources\TermResource\Pages\Concerns;

use App\Enums\SenseRelationType;
use App\Enums\TermStatus;
use App\Models\Term;
use App\Models\TermVersion;
use App\Models\TermVersionSense;
use App\Models\TermVersionSenseRelation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ManagesSpanishVersion
{
    protected function pullSpanishVersionData(array &$data): array
    {
        return Arr::pull($data, 'es_version', []);
    }

    protected function ensureTermSlug(array $data, array $esVersionData): array
    {
        if (filled($esVersionData['title'] ?? null)) {
            $data['slug'] = Str::slug($esVersionData['title']);
        }

        if (blank($data['slug'] ?? null)) {
            throw ValidationException::withMessages([
                'slug' => 'El slug es obligatorio o debe existir un título en español para generarlo.',
            ]);
        }

        return $data;
    }

    protected function ensurePublishedAtWhenPublished(array $data): array
    {
        $status = $data['status'] ?? null;
        $statusValue = $status instanceof TermStatus ? $status->value : (string) $status;

        if ($statusValue === TermStatus::Published->value && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function syncSpanishWorkingDraftVersion(Term $term, array $data): void
    {
        $senses = $this->normalizeSenseData($data['senses'] ?? []);

        if (blank($data['title'] ?? null) || $senses === []) {
            return;
        }

        $version = $term->versions()
            ->where('language_code', 'es')
            ->orderByDesc('version_number')
            ->first();

        if (! $version instanceof TermVersion) {
            $nextVersionNumber = (int) $term->versions()
                ->where('language_code', 'es')
                ->max('version_number') + 1;

            $version = $term->versions()->create([
                'language_code' => 'es',
                'version_number' => max(1, $nextVersionNumber),
                'created_by' => Auth::id(),
                'title' => (string) $data['title'],
                'notes' => $data['notes'] ?? null,
                'reviewed_by' => null,
                'approved_at' => null,
            ]);
        } else {
            $version->fill([
                'title' => (string) $data['title'],
                'notes' => $data['notes'] ?? null,
                'reviewed_by' => null,
                'approved_at' => null,
            ]);

            if (! $version->created_by) {
                $version->created_by = Auth::id();
            }

            $version->save();
        }

        $this->syncVersionSenses($version, $senses);

        if ($term->current_version_id !== $version->id) {
            $term->forceFill(['current_version_id' => $version->id])->save();
        }
    }

    protected function getSpanishVersionFormData(Term $term): array
    {
        $version = $term->versions()
            ->where('language_code', 'es')
            ->orderByDesc('version_number')
            ->first();

        if (! $version instanceof TermVersion) {
            return [];
        }

        $version->loadMissing('senses');
        $version->loadMissing('senses.relations');

        $senses = $version->senses
            ->sortBy('sense_number')
            ->values()
            ->map(fn (TermVersionSense $sense): array => [
                'definition' => $sense->definition,
                'relations' => $sense->relations
                    ->values()
                    ->map(fn (TermVersionSenseRelation $relation): array => [
                        'related_term_id' => $relation->related_term_id,
                        'relation_type' => $relation->relation_type instanceof SenseRelationType
                            ? $relation->relation_type->value
                            : (string) $relation->relation_type,
                    ])
                    ->all(),
                    ])
                    ->all();

        return [
            'title' => $version->title,
            'senses' => $senses,
            'notes' => $version->notes,
        ];
    }

    /**
     * @return array<int, array{definition: string, relations: array<int, array{related_term_id:int, relation_type:string}>}>
     */
    protected function normalizeSenseData(mixed $senses): array
    {
        return Collection::wrap($senses)
            ->map(function (mixed $row): ?array {
                if (! is_array($row)) {
                    return null;
                }

                $relations = Collection::wrap($row['relations'] ?? [])
                    ->map(function (mixed $relationRow): ?array {
                        if (! is_array($relationRow)) {
                            return null;
                        }

                        $relatedTermId = (int) ($relationRow['related_term_id'] ?? 0);
                        $relationType = (string) ($relationRow['relation_type'] ?? '');

                        if ($relatedTermId < 1 || SenseRelationType::tryFrom($relationType) === null) {
                            return null;
                        }

                        return [
                            'related_term_id' => $relatedTermId,
                            'relation_type' => $relationType,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();

                $definition = trim((string) ($row['definition'] ?? ''));

                if ($definition === '' && $relations === []) {
                    return null;
                }

                return [
                    'definition' => $definition,
                    'relations' => $relations,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{definition: string, relations: array<int, array{related_term_id:int, relation_type:string}>}>  $senses
     */
    protected function syncVersionSenses(TermVersion $version, array $senses): void
    {
        $version->senses()->delete();

        if ($senses === []) {
            return;
        }

        $now = now();

        $createdSenses = $version->senses()->createMany(
            collect($senses)
                ->values()
                ->map(fn (array $sense, int $index): array => [
                    'sense_number' => $index + 1,
                    'definition' => $sense['definition'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
        );

        foreach ($createdSenses as $index => $createdSense) {
            $senseRelations = $senses[$index]['relations'] ?? [];

            if ($senseRelations === []) {
                continue;
            }

            $createdSense->relations()->createMany(
                collect($senseRelations)->map(fn (array $relation): array => [
                    'related_term_id' => $relation['related_term_id'],
                    'relation_type' => $relation['relation_type'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );
        }
    }
}
