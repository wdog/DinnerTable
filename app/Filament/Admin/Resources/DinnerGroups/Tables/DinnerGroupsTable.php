<?php

namespace App\Filament\Admin\Resources\DinnerGroups\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class DinnerGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('group_image')
                    ->disk('public')
                    ->defaultImageUrl(url('/images/default-group.svg'))
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slogan')
                    ->label('Slogan')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('group_code')
                    ->label('Codice Invito')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('creator.name')
                    ->label('Creato da')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('members_count')
                    ->label('Membri')
                    ->counts('members')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
