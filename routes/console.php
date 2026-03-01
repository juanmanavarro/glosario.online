<?php

use App\Enums\SenseRelationType;
use App\Enums\TermStatus;
use App\Models\Category;
use App\Models\Term;
use App\Models\TermVersion;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('glossary:import-abogacia {path=resources/data/abogacia.json}', function (string $path): int {
    $fullPath = base_path($path);

    if (! is_file($fullPath)) {
        $this->error("No existe el archivo: {$fullPath}");

        return Command::FAILURE;
    }

    $contents = file_get_contents($fullPath);

    if ($contents === false) {
        $this->error("No se pudo leer el archivo: {$fullPath}");

        return Command::FAILURE;
    }

    $payload = json_decode($contents, true);

    if (! is_array($payload)) {
        $this->error('El JSON no contiene una lista válida de términos.');

        return Command::FAILURE;
    }

    $existingCategoryIds = Category::query()->pluck('id')->all();

    $created = 0;
    $updated = 0;
    $createdPlaceholders = 0;

    $resolveRelatedTerm = function (string $relatedTitle) use (&$createdPlaceholders): Term {
        $normalizedTitle = trim($relatedTitle);

        if ($normalizedTitle === '') {
            throw new RuntimeException('No se puede crear una relación con un término vacío.');
        }

        $relatedSlug = Str::slug($normalizedTitle);

        if ($relatedSlug === '') {
            throw new RuntimeException("No se pudo generar el slug para el término relacionado \"{$normalizedTitle}\".");
        }

        $existingTerm = Term::query()
            ->where('slug', $relatedSlug)
            ->orWhereHas('versions', function ($query) use ($normalizedTitle) {
                $query
                    ->where('language_code', 'es')
                    ->whereRaw('LOWER(title) = ?', [Str::lower($normalizedTitle)]);
            })
            ->first();

        if ($existingTerm instanceof Term) {
            return $existingTerm;
        }

        $term = Term::query()->create([
            'slug' => $relatedSlug,
            'title_en' => null,
            'status' => 'draft',
            'current_version_id' => null,
            'published_at' => null,
        ]);

        $version = $term->versions()->create([
            'language_code' => 'es',
            'version_number' => 1,
            'title' => $normalizedTitle,
            'notes' => null,
            'created_by' => null,
            'reviewed_by' => null,
            'approved_at' => null,
        ]);

        $term->forceFill([
            'current_version_id' => $version->id,
        ])->save();

        $createdPlaceholders++;

        return $term;
    };

    foreach ($payload as $index => $row) {
        if (! is_array($row)) {
            $this->error('Entrada inválida en la posición '.($index + 1).'.');

            return Command::FAILURE;
        }

        $esVersion = is_array($row['es_version'] ?? null) ? $row['es_version'] : [];
        $title = trim((string) ($esVersion['title'] ?? ''));

        if ($title === '') {
            $this->error('Falta `es_version.title` en la posición '.($index + 1).'.');

            return Command::FAILURE;
        }

        $slug = Str::slug($title);

        if ($slug === '') {
            $this->error("No se pudo generar el slug para \"{$title}\".");

            return Command::FAILURE;
        }

        $statusValue = (string) ($row['status'] ?? 'review');
        $status = TermStatus::tryFrom($statusValue) ?? TermStatus::Review;

        $publishedAt = filled($row['published_at'] ?? null)
            ? Carbon::parse((string) $row['published_at'])
            : null;

        try {
            DB::transaction(function () use (
                $row,
                $title,
                $slug,
                $status,
                $publishedAt,
                $existingCategoryIds,
                $resolveRelatedTerm,
                &$created,
                &$updated
            ): void {
                $term = Term::query()->firstOrNew(['slug' => $slug]);
                $wasCreated = ! $term->exists;

                $term->fill([
                    'slug' => $slug,
                    'title_en' => filled($row['title_en'] ?? null) ? trim((string) $row['title_en']) : null,
                    'status' => $status->value,
                    'published_at' => $publishedAt,
                ]);
                $term->save();

                $version = $term->versions()
                    ->where('language_code', 'es')
                    ->orderByDesc('version_number')
                    ->first();

                $notes = filled($row['es_version']['notes'] ?? null)
                    ? trim((string) $row['es_version']['notes'])
                    : null;

                $approvedAt = $status === TermStatus::Published ? ($publishedAt ?? now()) : null;

                if (! $version instanceof TermVersion) {
                    $version = $term->versions()->create([
                        'language_code' => 'es',
                        'version_number' => 1,
                        'title' => $title,
                        'notes' => $notes,
                        'created_by' => null,
                        'reviewed_by' => null,
                        'approved_at' => $approvedAt,
                    ]);
                } else {
                    $version->fill([
                        'title' => $title,
                        'notes' => $notes,
                        'reviewed_by' => null,
                        'approved_at' => $approvedAt,
                    ]);
                    $version->save();
                }

                $senses = collect($row['es_version']['senses'] ?? [])
                    ->filter(fn (mixed $sense): bool => is_array($sense))
                    ->map(fn (array $sense): array => [
                        'definition' => trim((string) ($sense['definition'] ?? '')),
                        'relations' => collect($sense['relations'] ?? [])
                            ->filter(fn (mixed $relation): bool => is_array($relation))
                            ->values()
                            ->all(),
                    ])
                    ->filter(fn (array $sense): bool => $sense['definition'] !== '')
                    ->filter()
                    ->values();

                if ($senses->isEmpty()) {
                    throw new RuntimeException("El término \"{$title}\" no tiene acepciones válidas.");
                }

                $version->senses()->delete();

                $version->senses()->createMany(
                    $senses->map(fn (array $sense, int $senseIndex): array => [
                        'sense_number' => $senseIndex + 1,
                        'definition' => $sense['definition'],
                    ])->all()
                );

                $version->load('senses.relations');

                $persistedSenses = $version->senses
                    ->sortBy('sense_number')
                    ->values();

                foreach ($persistedSenses as $senseIndex => $persistedSense) {
                    $persistedSense->relations()->delete();

                    $rows = [];
                    $seen = [];

                    foreach (($senses[$senseIndex]['relations'] ?? []) as $relation) {
                        $relationType = SenseRelationType::tryFrom((string) ($relation['type'] ?? ''));
                        $relatedTitle = trim((string) ($relation['term'] ?? ''));

                        if (! $relationType instanceof SenseRelationType || $relatedTitle === '') {
                            continue;
                        }

                        $relatedTerm = $resolveRelatedTerm($relatedTitle);

                        if ($relatedTerm->id === $term->id) {
                            continue;
                        }

                        $key = $relatedTerm->id.'|'.$relationType->value;

                        if (isset($seen[$key])) {
                            continue;
                        }

                        $seen[$key] = true;

                        $rows[] = [
                            'related_term_id' => $relatedTerm->id,
                            'relation_type' => $relationType->value,
                        ];
                    }

                    if ($rows !== []) {
                        $persistedSense->relations()->createMany($rows);
                    }
                }

                $term->forceFill([
                    'current_version_id' => $version->id,
                    'status' => $status->value,
                    'published_at' => $status === TermStatus::Published ? ($publishedAt ?? now()) : null,
                ])->save();

                $categoryIds = collect($row['categories'] ?? [])
                    ->map(fn (mixed $categoryId): int => (int) $categoryId)
                    ->filter(fn (int $categoryId): bool => $categoryId > 0)
                    ->filter(fn (int $categoryId): bool => in_array($categoryId, $existingCategoryIds, true))
                    ->unique()
                    ->values()
                    ->all();

                $term->categories()->sync($categoryIds);

                $term->keywords()->delete();

                $keywords = collect($row['keywords'] ?? [])
                    ->map(fn (mixed $keyword): string => trim((string) $keyword))
                    ->filter()
                    ->unique()
                    ->values();

                if ($keywords->isNotEmpty()) {
                    $term->keywords()->createMany(
                        $keywords->map(fn (string $keyword): array => [
                            'keyword' => $keyword,
                        ])->all()
                    );
                }

                if ($wasCreated) {
                    $created++;

                    return;
                }

                $updated++;
            });
        } catch (Throwable $e) {
            $this->error('Error en la posición '.($index + 1).': '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    $this->info("Importación completada. Creados: {$created}. Actualizados: {$updated}. Relacionados creados: {$createdPlaceholders}.");

    return Command::SUCCESS;
})->purpose('Importa resources/data/abogacia.json a la base de datos');
