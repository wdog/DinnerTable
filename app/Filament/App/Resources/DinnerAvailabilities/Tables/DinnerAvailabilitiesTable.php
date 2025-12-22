<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Illuminate\Support\HtmlString;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                // !
                TextColumn::make('dinnerDate.dinner_date')
                    ->date('Y M, d')
                    ->icon('tabler-calendar')
                    ->sortable(),
                // !
                TextColumn::make('user.name')
                    ->searchable(),
                // !
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                // !
                IconColumn::make('can_host')
                    ->label('Può ospitare')
                    ->alignCenter()
                    ->trueIcon('tabler-chef-hat-filled')
                    ->falseIcon('tabler-tools-kitchen-3')
                    ->trueColor('success')
                    ->falseColor('info')
                    ->boolean(),
                // !

                TextColumn::make('max_guests')
                    ->label('Max ospitabili')
                    ->alignCenter()
                    ->badge(),
                // !
                TextColumn::make('bookings_count')
                    ->label('Prenotazioni')
                    ->alignCenter()
                    ->badge(fn ($record) => $record->can_host)
                    ->counts('bookings')
                    ->formatStateUsing(
                        fn ($record): HtmlString|string|null => $record->can_host ?
                            new HtmlString(
                                "<div class='text-left'>" .
                                    '<div>Confermati: ' . $record->bookings()->where('status', 'confirmed')->sum('guests_count') . '</div>' .
                                    '<div> Prenotati: ' . $record->bookings->sum('guests_count') . '</div>' .
                                    '</div>'
                            ) : ''
                    ),
                // !
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // !
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
            ])
            ->emptyStateHeading('Nessuna Disponibilità per ospitare o essere ospitato')
            ->emptyStateDescription('Non hai ancora indicato una disponibilità. Aggiungi una data!')
            ->emptyStateIcon('tabler-calendar-off');
    }
}
