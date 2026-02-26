<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermVersionResource\Pages\ListTermVersions;
use App\Models\TermVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class TermVersionResource extends Resource
{
    protected static ?string $model = TermVersion::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Versiones';

    protected static string | UnitEnum | null $navigationGroup = 'Diccionario';

    protected static ?int $navigationSort = 80;

    protected static ?string $modelLabel = 'versión';

    protected static ?string $pluralModelLabel = 'versiones';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('term.slug')
                    ->label('Término')
                    ->searchable(),
                TextColumn::make('language_code')
                    ->label('Idioma')
                    ->sortable(),
                TextColumn::make('version_number')
                    ->label('Versión')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(80),
                TextColumn::make('creator.name')
                    ->label('Creado por')
                    ->placeholder('-'),
                TextColumn::make('approved_at')
                    ->label('Aprobado')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('language_code')
                    ->label('Idioma')
                    ->options(fn (): array => TermVersion::query()
                        ->distinct()
                        ->orderBy('language_code')
                        ->pluck('language_code', 'language_code')
                        ->all()),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTermVersions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
