<?php

namespace App\Filament\Resources;

use App\Enums\SenseRelationType;
use App\Enums\TermStatus;
use App\Filament\Resources\TermResource\Pages\CreateTerm;
use App\Filament\Resources\TermResource\Pages\EditTerm;
use App\Filament\Resources\TermResource\Pages\ListTerms;
use App\Models\Category;
use App\Models\Term;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Términos';

    protected static string | UnitEnum | null $navigationGroup = 'Diccionario';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'slug';

    protected static ?string $modelLabel = 'término';

    protected static ?string $pluralModelLabel = 'términos';

    protected static ?string $slug = 'terms';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options([
                        TermStatus::Draft->value => 'Borrador',
                        TermStatus::Review->value => 'Revisión',
                        TermStatus::Published->value => 'Publicado',
                        TermStatus::Archived->value => 'Archivado',
                    ])
                    ->default(TermStatus::Draft->value)
                    ->live()
                    ->afterStateUpdated(function (\Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set, mixed $state): void {
                        $status = $state instanceof TermStatus ? $state->value : (string) $state;

                        if ($status === TermStatus::Published->value && blank($get('published_at'))) {
                            $set('published_at', now());
                        }
                    }),
                DateTimePicker::make('published_at')
                    ->label('Publicado el')
                    ->seconds(false),
                Select::make('categories')
                    ->label('Categorías')
                    ->relationship('categories', 'name')
                    ->default(fn (string $operation): array => $operation === 'create'
                        ? Category::query()
                            ->where('slug', 'sin-categoria')
                            ->pluck('id')
                            ->all()
                        : [])
                    ->getOptionLabelFromRecordUsing(fn (Category $record): string => $record->parent ? "{$record->parent->name} / {$record->name}" : $record->name)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Tabs::make('Contenido')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Versión (ES)')
                            ->schema([
                                TextInput::make('es_version.title')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (\Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set, ?string $state): void {
                                        if (filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->helperText('Se genera automáticamente al guardar a partir del título.')
                                    ->maxLength(255),
                                Repeater::make('es_version.senses')
                                    ->label('Acepciones')
                                    ->helperText('El orden de los elementos corresponde a las acepciones 1, 2, 3...')
                                    ->schema([
                                        Textarea::make('definition')
                                            ->label('Definición')
                                            ->required(function (\Filament\Schemas\Components\Utilities\Get $get): bool {
                                                $relations = collect($get('relations') ?? [])
                                                    ->filter(fn (mixed $row): bool => is_array($row) && (
                                                        filled($row['related_term_id'] ?? null) ||
                                                        filled($row['relation_type'] ?? null)
                                                    ));

                                                return $relations->isEmpty();
                                            })
                                            ->rows(4)
                                            ->helperText('Texto de la acepción (p. ej. la 1, 2, 3...).')
                                            ->columnSpanFull(),
                                        Repeater::make('relations')
                                            ->label('Relaciones de la acepción')
                                            ->schema([
                                                Select::make('related_term_id')
                                                    ->label('Término relacionado')
                                                    ->options(fn (): array => Term::query()
                                                        ->orderBy('slug')
                                                        ->pluck('slug', 'id')
                                                        ->all())
                                                    ->getSearchResultsUsing(function (?string $search): array {
                                                        $search = trim((string) $search);

                                                        if ($search === '') {
                                                            return Term::query()
                                                                ->orderBy('slug')
                                                                ->limit(50)
                                                                ->pluck('slug', 'id')
                                                                ->all();
                                                        }

                                                        $searchSlug = Str::slug($search);
                                                        $normalizedSearch = Str::lower($search);

                                                        $hasExactMatch = Term::query()
                                                            ->when($searchSlug !== '', fn (Builder $query): Builder => $query->where('slug', $searchSlug))
                                                            ->orWhereHas('currentVersion', function (Builder $query) use ($normalizedSearch): Builder {
                                                                return $query->whereRaw('LOWER(title) = ?', [$normalizedSearch]);
                                                            })
                                                            ->exists();

                                                        if (! $hasExactMatch) {
                                                            return [
                                                                "__create__:{$search}" => "Crear \"{$search}\" como término borrador",
                                                            ];
                                                        }

                                                        return Term::query()
                                                            ->where('slug', 'like', "%{$search}%")
                                                            ->orWhereHas('currentVersion', fn (Builder $query): Builder => $query->where('title', 'like', "%{$search}%"))
                                                            ->orderBy('slug')
                                                            ->limit(50)
                                                            ->pluck('slug', 'id')
                                                            ->all();
                                                    })
                                                    ->getOptionLabelUsing(function (mixed $value): ?string {
                                                        if (! is_string($value) || ! str_starts_with($value, '__create__:')) {
                                                            return Term::query()->find($value)?->slug;
                                                        }

                                                        return 'Crear término';
                                                    })
                                                    ->afterStateUpdated(function (\Filament\Schemas\Components\Utilities\Set $set, mixed $state): void {
                                                        if (! is_string($state) || ! str_starts_with($state, '__create__:')) {
                                                            return;
                                                        }

                                                        $title = trim(Str::after($state, '__create__:'));
                                                        $slug = Str::slug($title);

                                                        if ($title === '' || $slug === '') {
                                                            $set('related_term_id', null);

                                                            return;
                                                        }

                                                        $term = Term::query()->firstOrCreate(
                                                            ['slug' => $slug],
                                                            [
                                                                'status' => TermStatus::Draft->value,
                                                                'published_at' => null,
                                                                'current_version_id' => null,
                                                            ]
                                                        );

                                                        if ($term->wasRecentlyCreated) {
                                                            $version = $term->versions()->create([
                                                                'language_code' => 'es',
                                                                'title' => $title,
                                                                'definition' => '',
                                                                'notes' => null,
                                                                'created_by' => Auth::id(),
                                                                'reviewed_by' => null,
                                                                'approved_at' => null,
                                                            ]);

                                                            $term->forceFill([
                                                                'current_version_id' => $version->id,
                                                            ])->save();

                                                            $defaultCategoryId = Category::query()
                                                                ->where('slug', 'sin-categoria')
                                                                ->value('id');

                                                            if ($defaultCategoryId) {
                                                                $term->categories()->syncWithoutDetaching([$defaultCategoryId]);
                                                            }
                                                        } elseif (! $term->current_version_id) {
                                                            $version = $term->versions()
                                                                ->where('language_code', 'es')
                                                                ->orderByDesc('version_number')
                                                                ->first();

                                                            if (! $version) {
                                                                $version = $term->versions()->create([
                                                                    'language_code' => 'es',
                                                                    'title' => $title,
                                                                    'definition' => '',
                                                                    'notes' => null,
                                                                    'created_by' => Auth::id(),
                                                                    'reviewed_by' => null,
                                                                    'approved_at' => null,
                                                                ]);
                                                            }

                                                            $term->forceFill([
                                                                'current_version_id' => $version->id,
                                                            ])->save();
                                                        }

                                                        $set('related_term_id', $term->id);
                                                    })
                                                    ->live()
                                                    ->searchDebounce(250)
                                                    ->searchPrompt('Escribe para buscar o crear un término')
                                                    ->searchingMessage('Buscando término...')
                                                    ->loadingMessage('Buscando término...')
                                                    ->searchable()
                                                    ->required(),
                                                Select::make('relation_type')
                                                    ->label('Tipo')
                                                    ->options(self::getSenseRelationTypeLabels())
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ])
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->reorderableWithButtons()
                                    ->columnSpanFull(),
                                Textarea::make('es_version.notes')
                                    ->label('Notas')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
                Repeater::make('keywords')
                    ->label('Keywords')
                    ->relationship()
                    ->schema([
                        TextInput::make('keyword')
                            ->label('Keyword')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('currentVersion.title')
                    ->label('Título (ES)')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('currentVersion', fn (Builder $versionQuery): Builder => $versionQuery->where('title', 'like', "%{$search}%"));
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (TermStatus | string | null $state): string => match ($state instanceof TermStatus ? $state : TermStatus::tryFrom((string) $state)) {
                        TermStatus::Draft => 'Borrador',
                        TermStatus::Review => 'Revisión',
                        TermStatus::Published => 'Publicado',
                        TermStatus::Archived => 'Archivado',
                        default => '-',
                    })
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publicado el')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        TermStatus::Draft->value => 'Borrador',
                        TermStatus::Review->value => 'Revisión',
                        TermStatus::Published->value => 'Publicado',
                        TermStatus::Archived->value => 'Archivado',
                    ]),
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getTermStatusLabels(): array
    {
        return [
            TermStatus::Draft->value => 'Borrador',
            TermStatus::Review->value => 'Revisión',
            TermStatus::Published->value => 'Publicado',
            TermStatus::Archived->value => 'Archivado',
        ];
    }

    public static function getSenseRelationTypeLabels(): array
    {
        return [
            SenseRelationType::See->value => 'Véase',
            SenseRelationType::SeeAlso->value => 'Véase también',
            SenseRelationType::Synonym->value => 'Sinónimo',
            SenseRelationType::Antonym->value => 'Antónimo',
            SenseRelationType::Related->value => 'Relacionado',
            SenseRelationType::Broader->value => 'Más amplio',
            SenseRelationType::Narrower->value => 'Más específico',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTerms::route('/'),
            'create' => CreateTerm::route('/create'),
            'edit' => EditTerm::route('/{record}/edit'),
        ];
    }
}
