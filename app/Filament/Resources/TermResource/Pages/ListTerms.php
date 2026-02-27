<?php

namespace App\Filament\Resources\TermResource\Pages;

use App\Enums\TermStatus;
use App\Filament\Resources\TermResource;
use App\Models\Category;
use App\Models\Term;
use App\Models\TermVersion;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    private const IMPORT_HEADERS = [
        'Título',
        'Título (EN)',
        'Acepciones',
    ];

    private const RELATION_PATTERNS = [
        [
            'type' => 'see_also',
            'pattern' => '/\b(?:Véase|Véanse)\s+también\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
        [
            'type' => 'see',
            'pattern' => '/\b(?:Véase|Véanse)(?!\s+también)\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
        [
            'type' => 'synonym',
            'pattern' => '/\bSinónimos?\s*:?\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
        [
            'type' => 'antonym',
            'pattern' => '/\bAntónimos?\s*:?\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
        [
            'type' => 'related',
            'pattern' => '/\bRelacionados?\s*:?\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
        [
            'type' => 'broader',
            'pattern' => '/\bMás amplio\s*:?\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
        [
            'type' => 'narrower',
            'pattern' => '/\bMás específico\s*:?\s+(.+?)(?=(?:[.;]|$))/iu',
        ],
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importXlsx')
                ->label('Importar XLSX')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false)
                ->modalHeading('Importar términos')
                ->modalSubmitActionLabel('Importar')
                ->form([
                    FileUpload::make('file')
                        ->label('Archivo XLSX')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->maxFiles(1)
                        ->rules([
                            function (): \Closure {
                                return function (string $attribute, mixed $value, \Closure $fail): void {
                                    try {
                                        $this->validateImportFileHeader($value);
                                    } catch (\Throwable $exception) {
                                        $fail($exception->getMessage());
                                    }
                                };
                            },
                        ])
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data): void {
                    try {
                        $summary = $this->importTerms($data['file'] ?? null);
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Error en la importación')
                            ->body($exception->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    $notification = Notification::make()
                        ->title($summary['errors'] === [] ? 'Importación completada' : 'Importación completada con errores')
                        ->body(implode("\n", [
                            "Procesadas: {$summary['processed']}",
                            "Creadas: {$summary['created']}",
                            "Actualizadas: {$summary['updated']}",
                            "Provisionales: {$summary['placeholders']}",
                            'Errores: ' . count($summary['errors']),
                        ]));

                    if ($summary['errors'] === []) {
                        $notification->success();
                    } else {
                        $notification->warning();
                    }

                    $notification->send();
                }),
            CreateAction::make(),
        ];
    }

    /**
     * @return array{processed:int,created:int,updated:int,placeholders:int,errors:array<int,string>}
     */
    private function importTerms(mixed $file): array
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $path = $this->resolveUploadedFilePath($file);

        if ($path === null) {
            throw new \RuntimeException('No se pudo leer el archivo subido.');
        }

        $rows = $this->readXlsxRows($path);

        if ($rows === []) {
            throw new \RuntimeException('El archivo XLSX está vacío.');
        }

        $headers = array_map(static fn (string $value): string => trim($value), $rows[0]);

        if ($headers !== self::IMPORT_HEADERS) {
            throw new \RuntimeException('Las columnas del archivo no son las correctas. El encabezado debe ser exactamente: Título, Título (EN), Acepciones.');
        }

        $defaultCategoryId = Category::query()
            ->where('slug', 'sin-categoria')
            ->value('id');

        if (! $defaultCategoryId) {
            throw new \RuntimeException('No existe la categoría por defecto "sin-categoria".');
        }

        $summary = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'placeholders' => 0,
            'errors' => [],
        ];

        $relationSpecsByTerm = [];

        foreach (array_slice($rows, 1) as $index => $row) {
            $rowNumber = $index + 2;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $summary['processed']++;

            try {
                $mappedRow = $this->mapImportRow($row);
                $result = DB::transaction(fn (): array => $this->upsertImportedTerm($mappedRow, $defaultCategoryId));

                if ($result['created']) {
                    $summary['created']++;
                } else {
                    $summary['updated']++;
                }

                $relationSpecsByTerm[$result['term_id']] = $result['relation_specs'];
            } catch (\Throwable $exception) {
                $summary['errors'][] = "Fila {$rowNumber}: {$exception->getMessage()}";
            }
        }

        foreach ($relationSpecsByTerm as $termId => $relationSpecs) {
            try {
                $summary['placeholders'] += DB::transaction(
                    fn (): int => $this->syncImportedRelations((int) $termId, $relationSpecs)
                );
            } catch (\Throwable $exception) {
                $summary['errors'][] = "Relaciones del término #{$termId}: {$exception->getMessage()}";
            }
        }

        return $summary;
    }

    private function validateImportFileHeader(mixed $file): void
    {
        $path = $this->resolveUploadedFilePath($file);

        if ($path === null) {
            throw new \RuntimeException('No se pudo leer el archivo subido.');
        }

        $headers = $this->readXlsxHeaderRow($path);

        if ($headers !== self::IMPORT_HEADERS) {
            throw new \RuntimeException('Las columnas del archivo no son las correctas. El encabezado debe ser exactamente: Título, Título (EN), Acepciones.');
        }
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, string>  $row
     * @return array{title:string,title_en:?string,senses:array<int,array{definition:string,relations:array<int,array{type:string,title:string}>}>}
     */
    private function mapImportRow(array $row): array
    {
        $title = trim((string) ($row[0] ?? ''));

        if ($title === '') {
            throw new \RuntimeException('El título es obligatorio.');
        }

        $senses = $this->parseSenses((string) ($row[2] ?? ''));

        if ($senses === []) {
            throw new \RuntimeException('La columna Acepciones no puede estar vacía.');
        }

        $titleEn = trim((string) ($row[1] ?? ''));

        return [
            'title' => $title,
            'title_en' => $titleEn !== '' ? $titleEn : null,
            'senses' => $senses,
        ];
    }

    /**
     * @param  array{title:string,title_en:?string,senses:array<int,array{definition:string,relations:array<int,array{type:string,title:string}>}>}  $mappedRow
     * @return array{created:bool,term_id:int,relation_specs:array<int,array<int,array{type:string,title:string}>>}
     */
    private function upsertImportedTerm(array $mappedRow, int $defaultCategoryId): array
    {
        $slug = Str::slug($mappedRow['title']);

        if ($slug === '') {
            throw new \RuntimeException('No se pudo generar el slug a partir del título.');
        }

        $term = Term::query()->firstOrNew(['slug' => $slug]);
        $created = ! $term->exists;

        $term->fill([
            'slug' => $slug,
            'title_en' => $mappedRow['title_en'],
            'status' => TermStatus::Review->value,
            'published_at' => null,
        ]);
        $term->save();

        $version = $this->upsertSpanishVersion($term, $mappedRow['title'], $mappedRow['senses']);

        if ($term->current_version_id !== $version->id) {
            $term->forceFill([
                'current_version_id' => $version->id,
            ])->save();
        }

        $term->categories()->sync([$defaultCategoryId]);
        $term->keywords()->delete();

        $this->syncSenseDefinitions($version, $mappedRow['senses']);

        return [
            'created' => $created,
            'term_id' => (int) $term->id,
            'relation_specs' => array_map(
                static fn (array $sense): array => $sense['relations'],
                $mappedRow['senses']
            ),
        ];
    }

    /**
     * @param  array<int,array{definition:string,relations:array<int,array{type:string,title:string}>}>  $senses
     */
    private function upsertSpanishVersion(Term $term, string $title, array $senses): TermVersion
    {
        $version = $term->versions()
            ->where('language_code', 'es')
            ->orderByDesc('version_number')
            ->first();

        $fallbackDefinition = $this->buildFallbackDefinition($senses);

        if (! $version instanceof TermVersion) {
            $versionNumber = (int) $term->versions()
                ->where('language_code', 'es')
                ->max('version_number') + 1;

            return $term->versions()->create([
                'language_code' => 'es',
                'version_number' => max(1, $versionNumber),
                'created_by' => Auth::id(),
                'title' => $title,
                'definition' => $fallbackDefinition,
                'notes' => null,
                'reviewed_by' => null,
                'approved_at' => null,
            ]);
        }

        $version->fill([
            'title' => $title,
            'definition' => $fallbackDefinition,
            'notes' => null,
            'reviewed_by' => null,
            'approved_at' => null,
        ]);

        if (! $version->created_by) {
            $version->created_by = Auth::id();
        }

        $version->save();

        return $version;
    }

    /**
     * @param  array<int,array{definition:string,relations:array<int,array{type:string,title:string}>}>  $senses
     */
    private function buildFallbackDefinition(array $senses): string
    {
        foreach ($senses as $sense) {
            if ($sense['definition'] !== '') {
                return $sense['definition'];
            }
        }

        return '';
    }

    /**
     * @param  array<int,array{definition:string,relations:array<int,array{type:string,title:string}>}>  $senses
     */
    private function syncSenseDefinitions(TermVersion $version, array $senses): void
    {
        $version->senses()->delete();

        $rows = collect($senses)
            ->values()
            ->map(fn (array $sense, int $index): array => [
                'sense_number' => $index + 1,
                'definition' => $sense['definition'],
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if ($rows !== []) {
            $version->senses()->createMany($rows);
        }
    }

    /**
     * @param  array<int,array<int,array{type:string,title:string}>>  $relationSpecs
     */
    private function syncImportedRelations(int $termId, array $relationSpecs): int
    {
        $term = Term::query()->find($termId);

        if (! $term instanceof Term) {
            return 0;
        }

        $version = $term->versions()
            ->where('language_code', 'es')
            ->orderByDesc('version_number')
            ->first();

        if (! $version instanceof TermVersion) {
            return 0;
        }

        $version->load('senses.relations');

        $senses = $version->senses
            ->sortBy('sense_number')
            ->values();

        $createdPlaceholders = 0;

        foreach ($senses as $index => $sense) {
            $sense->relations()->delete();

            $rows = [];
            $seen = [];

            foreach ($relationSpecs[$index] ?? [] as $relationSpec) {
                $relatedTerm = $this->findOrCreateRelatedTerm($relationSpec['title'], $createdPlaceholders);

                if ($relatedTerm->id === $term->id) {
                    continue;
                }

                $key = $relatedTerm->id . '|' . $relationSpec['type'];

                if (isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;

                $rows[] = [
                    'related_term_id' => $relatedTerm->id,
                    'relation_type' => $relationSpec['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($rows !== []) {
                $sense->relations()->createMany($rows);
            }
        }

        return $createdPlaceholders;
    }

    private function findOrCreateRelatedTerm(string $title, int &$createdPlaceholders): Term
    {
        $normalizedTitle = trim($title);

        if ($normalizedTitle === '') {
            throw new \RuntimeException('No se pudo resolver un término relacionado vacío.');
        }

        $slug = Str::slug($normalizedTitle);

        if ($slug === '') {
            throw new \RuntimeException("No se pudo generar el slug para el término relacionado \"{$normalizedTitle}\".");
        }

        $existingTerm = Term::query()
            ->where('slug', $slug)
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
            'slug' => $slug,
            'title_en' => null,
            'status' => TermStatus::Draft->value,
            'current_version_id' => null,
            'published_at' => null,
        ]);

        $version = $term->versions()->create([
            'language_code' => 'es',
            'version_number' => 1,
            'created_by' => Auth::id(),
            'title' => $normalizedTitle,
            'definition' => '',
            'notes' => null,
            'reviewed_by' => null,
            'approved_at' => null,
        ]);

        $term->forceFill([
            'current_version_id' => $version->id,
        ])->save();

        $createdPlaceholders++;

        return $term;
    }

    /**
     * @return array<int,array{definition:string,relations:array<int,array{type:string,title:string}>}>
     */
    private function parseSenses(string $rawValue): array
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $rawValue) ?? '');

        if ($normalized === '') {
            return [];
        }

        $definitions = [];

        if (preg_match('/^\s*1\.\s+/u', $normalized) === 1) {
            preg_match_all('/(?:^|\s)(\d+)\.\s*(.*?)(?=(?:\s+\d+\.\s)|$)/su', $normalized, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $definition = trim($match[2] ?? '');

                if ($definition !== '') {
                    $definitions[] = $definition;
                }
            }
        }

        if ($definitions === []) {
            $definitions[] = $normalized;
        }

        return array_map(fn (string $definition): array => [
            'definition' => $definition,
            'relations' => $this->extractRelations($definition),
        ], $definitions);
    }

    /**
     * @return array<int,array{type:string,title:string}>
     */
    private function extractRelations(string $definition): array
    {
        $relations = [];

        foreach (self::RELATION_PATTERNS as $pattern) {
            preg_match_all($pattern['pattern'], $definition, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                foreach ($this->splitRelationTargets($match[1] ?? '') as $target) {
                    $relations[] = [
                        'type' => $pattern['type'],
                        'title' => $target,
                    ];
                }
            }
        }

        return $relations;
    }

    /**
     * @return array<int,string>
     */
    private function splitRelationTargets(string $value): array
    {
        $cleaned = trim($value);

        if ($cleaned === '') {
            return [];
        }

        $parts = preg_split('/\s*(?:,|;|\by\b|\be\b)\s*/iu', $cleaned) ?: [];

        return collect($parts)
            ->map(static fn (string $part): string => trim($part, " \t\n\r\0\x0B.:;()[]\"'"))
            ->filter(static fn (string $part): bool => $part !== '')
            ->values()
            ->all();
    }

    private function resolveUploadedFilePath(mixed $file): ?string
    {
        if (is_array($file)) {
            $file = reset($file);
        }

        if (is_string($file) && $file !== '') {
            if (TemporaryUploadedFile::canUnserialize($file)) {
                $file = TemporaryUploadedFile::unserializeFromLivewireRequest($file);

                if ($file instanceof TemporaryUploadedFile) {
                    $path = $file->getRealPath();

                    if (is_string($path) && $path !== '' && is_file($path)) {
                        return $path;
                    }
                }
            }

            if (str_starts_with($file, DIRECTORY_SEPARATOR) && is_file($file)) {
                return $file;
            }

            $disk = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');
            $storage = Storage::disk((string) $disk);

            if (method_exists($storage, 'path')) {
                $candidate = $storage->path($file);

                if (is_string($candidate) && $candidate !== '' && is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        if (is_object($file)) {
            if (method_exists($file, 'getRealPath')) {
                $path = $file->getRealPath();

                if (is_string($path) && $path !== '' && is_file($path)) {
                    return $path;
                }
            }

            if (method_exists($file, 'getPathname')) {
                $path = $file->getPathname();

                if (is_string($path) && $path !== '' && is_file($path)) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function readXlsxHeaderRow(string $path): array
    {
        $rows = $this->readXlsxRows($path, 1);

        return $rows[0] ?? [];
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readXlsxRows(string $path, ?int $limit = null): array
    {
        $sharedStringsXml = $this->readZipEntry($path, 'xl/sharedStrings.xml', true);
        $sharedStrings = $this->parseSharedStrings($sharedStringsXml);
        $sheetPath = $this->resolveFirstWorksheetPath($path);
        $sheetXml = $this->readZipEntry($path, $sheetPath);
        $sheet = simplexml_load_string($sheetXml);

        if ($sheet === false) {
            throw new \RuntimeException('No se pudo leer la hoja del archivo XLSX.');
        }

        $sheet->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $xmlRows = $sheet->xpath('/main:worksheet/main:sheetData/main:row');

        if (! is_array($xmlRows)) {
            return [];
        }

        $rows = [];

        foreach ($xmlRows as $xmlRow) {
            $xmlRow->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $cells = $xmlRow->xpath('./main:c');

            if (! is_array($cells)) {
                $rows[] = [];

                continue;
            }

            $rowValues = [];
            $maxIndex = -1;

            foreach ($cells as $cell) {
                $reference = (string) ($cell['r'] ?? '');
                $letters = preg_replace('/[^A-Z]/', '', strtoupper($reference)) ?: '';
                $index = $letters !== '' ? $this->columnLettersToIndex($letters) : count($rowValues);

                $rowValues[$index] = trim($this->extractCellValue($cell, $sharedStrings));
                $maxIndex = max($maxIndex, $index);
            }

            if ($maxIndex >= 0) {
                for ($i = 0; $i <= $maxIndex; $i++) {
                    $rowValues[$i] ??= '';
                }

                ksort($rowValues);
            }

            $rows[] = array_values($rowValues);

            if ($limit !== null && count($rows) >= $limit) {
                break;
            }
        }

        return $rows;
    }

    private function resolveFirstWorksheetPath(string $path): string
    {
        $workbookXml = $this->readZipEntry($path, 'xl/workbook.xml');
        $relsXml = $this->readZipEntry($path, 'xl/_rels/workbook.xml.rels');

        $workbook = simplexml_load_string($workbookXml);
        $rels = simplexml_load_string($relsXml);

        if ($workbook === false || $rels === false) {
            throw new \RuntimeException('No se pudo leer la estructura del archivo XLSX.');
        }

        $workbook->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $sheets = $workbook->xpath('/main:workbook/main:sheets/main:sheet[1]');

        if (! is_array($sheets) || $sheets === []) {
            throw new \RuntimeException('El archivo XLSX no contiene hojas.');
        }

        $relationshipAttributes = $sheets[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $relationshipId = (string) ($relationshipAttributes['id'] ?? '');

        if ($relationshipId === '') {
            throw new \RuntimeException('No se pudo resolver la primera hoja del XLSX.');
        }

        $rels->registerXPathNamespace('pkg', 'http://schemas.openxmlformats.org/package/2006/relationships');
        $relationships = $rels->xpath('/pkg:Relationships/pkg:Relationship');

        if (! is_array($relationships) || $relationships === []) {
            throw new \RuntimeException('No se encontraron relaciones de hojas en el XLSX.');
        }

        foreach ($relationships as $relationship) {
            $attributes = $relationship->attributes();

            if ((string) ($attributes['Id'] ?? '') !== $relationshipId) {
                continue;
            }

            $target = (string) ($attributes['Target'] ?? '');

            if ($target === '') {
                break;
            }

            return str_starts_with($target, 'xl/')
                ? $target
                : 'xl/' . ltrim($target, '/');
        }

        throw new \RuntimeException('No se encontró la hoja principal del XLSX.');
    }

    /**
     * @return array<int, string>
     */
    private function parseSharedStrings(?string $xml): array
    {
        if (! is_string($xml) || trim($xml) === '') {
            return [];
        }

        $sharedStrings = simplexml_load_string($xml);

        if ($sharedStrings === false) {
            return [];
        }

        $sharedStrings->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $items = $sharedStrings->xpath('/main:sst/main:si');

        if (! is_array($items)) {
            return [];
        }

        $values = [];

        foreach ($items as $item) {
            $item->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = $item->xpath('.//main:t');

            if (! is_array($parts)) {
                $values[] = '';

                continue;
            }

            $values[] = implode('', array_map(static fn ($part): string => (string) $part, $parts));
        }

        return $values;
    }

    private function readZipEntry(string $path, string $entry, bool $optional = false): ?string
    {
        if (! function_exists('shell_exec')) {
            throw new \RuntimeException('El servidor no puede leer archivos XLSX porque shell_exec no está disponible.');
        }

        $command = sprintf(
            'unzip -p %s %s 2>/dev/null',
            escapeshellarg($path),
            escapeshellarg($entry)
        );

        $output = shell_exec($command);

        if (! is_string($output) || $output === '') {
            if ($optional) {
                return null;
            }

            throw new \RuntimeException("No se pudo leer {$entry} dentro del XLSX.");
        }

        return $output;
    }

    private function extractCellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $cell->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $attributes = $cell->attributes();
        $type = (string) ($attributes['t'] ?? '');

        if ($type === 'inlineStr') {
            $parts = $cell->xpath('./main:is//main:t');

            if (! is_array($parts)) {
                return '';
            }

            return implode('', array_map(static fn ($part): string => (string) $part, $parts));
        }

        $valueNodes = $cell->xpath('./main:v');
        $rawValue = is_array($valueNodes) && $valueNodes !== []
            ? (string) $valueNodes[0]
            : '';

        if ($type === 's') {
            $index = (int) $rawValue;

            return $sharedStrings[$index] ?? '';
        }

        return $rawValue;
    }

    private function columnLettersToIndex(string $letters): int
    {
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }
}
