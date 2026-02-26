<?php

namespace App\Filament\Resources;

use App\Enums\EditorialAction;
use App\Filament\Resources\EditorialLogResource\Pages\ListEditorialLogs;
use App\Models\EditorialLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class EditorialLogResource extends Resource
{
    protected static ?string $model = EditorialLog::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Logs editoriales';

    protected static string | UnitEnum | null $navigationGroup = 'Diccionario';

    protected static ?int $navigationSort = 90;

    protected static ?string $modelLabel = 'log editorial';

    protected static ?string $pluralModelLabel = 'logs editoriales';

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
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->formatStateUsing(fn (EditorialAction | string | null $state): string => match ($state instanceof EditorialAction ? $state : EditorialAction::tryFrom((string) $state)) {
                        EditorialAction::Created => 'Creado',
                        EditorialAction::Updated => 'Actualizado',
                        EditorialAction::Reviewed => 'Revisado',
                        EditorialAction::Approved => 'Aprobado',
                        EditorialAction::Published => 'Publicado',
                        EditorialAction::Archived => 'Archivado',
                        default => '-',
                    })
                    ->sortable(),
                TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Acción')
                    ->options([
                        EditorialAction::Created->value => 'Creado',
                        EditorialAction::Updated->value => 'Actualizado',
                        EditorialAction::Reviewed->value => 'Revisado',
                        EditorialAction::Approved->value => 'Aprobado',
                        EditorialAction::Published->value => 'Publicado',
                        EditorialAction::Archived->value => 'Archivado',
                    ]),
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
            'index' => ListEditorialLogs::route('/'),
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
