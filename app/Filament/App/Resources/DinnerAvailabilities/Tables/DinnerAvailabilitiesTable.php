<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DinnerAvailabilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('dinnerDate.dinner_date')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible()
                    ->getTitleFromRecordUsing(
                        fn (Model $record): string => 'Dinner del ' . $record->dinnerDate->dinner_date->format('d/m/Y')
                    ),
            ])
            ->defaultSort('dinnerDate.dinner_date', 'desc')
            ->columns([
                TextColumn::make('dinnerDate.dinner_date')
                    ->date('Y M, d')
                    ->icon('tabler-calendar')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                IconColumn::make('can_host')
                    ->label('Può ospitare')
                    ->alignCenter()
                    ->trueIcon('tabler-chef-hat-filled')
                    ->falseIcon('tabler-pacman')
                    ->trueColor('success')
                    ->falseColor('info')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
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
