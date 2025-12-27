<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\DinnerAvailabilityStatus;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Configurazione tabella disponibilità utente.
 *
 * Tabella Filament per visualizzare e gestire le disponibilità dell'utente
 * (sia HOST che GUEST). Include:
 *
 * Funzionalità:
 * - Raggruppamento per data cena
 * - Filtri: range date, stato, tipo (HOST/GUEST)
 * - Azioni: View (sempre), Edit (se autorizzato e data futura)
 * - Bulk delete con verifica policy
 * - Contatori prenotazioni per HOST
 *
 * Policy e sicurezza:
 * - EditAction verifica policy update + data futura
 * - DeleteBulkAction verifica policy delete per ogni record
 * - Notifiche dettagliate con motivi blocco
 *
 * @see DinnerAvailabilityResource
 * @see DinnerAvailabilityPolicy
 */
class DinnerAvailabilitiesTable
{
    /**
     * Configura la tabella Filament con colonne, filtri e azioni.
     *
     * @param  Table  $table  Istanza tabella Filament
     * @return Table Tabella configurata
     */
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
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                // !
                IconColumn::make('can_host')
                    ->label('Può ospitare')
                    ->alignCenter()
                    ->sortable()
                    ->trueIcon('tabler-chef-hat-filled')
                    ->falseIcon('tabler-tools-kitchen-3')
                    ->trueColor('success')
                    ->falseColor('info')
                    ->boolean(),
                // !

                TextColumn::make('max_guests')
                    ->label('Max ospitabili')
                    ->sortable()
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
                Filter::make('dinner_date')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Da')
                            ->native(false)
                            ->prefixIcon('tabler-calendar')
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleziona data inizio'),
                        DatePicker::make('until')
                            ->label('A')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleziona data fine'),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereHas(
                                    'dinnerDate',
                                    fn (Builder $query) => $query->whereDate('dinner_date', '>=', $date)
                                ),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereHas(
                                    'dinnerDate',
                                    fn (Builder $query) => $query->whereDate('dinner_date', '<=', $date)
                                ),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = 'Da: ' . \Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'A: ' . \Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(DinnerAvailabilityStatus::class)
                    ->native(false)
                    ->multiple()
                    ->preload(),

            ], FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => $record->dinnerDate->dinner_date->isFuture()
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $deleted = 0;
                            $skipped = 0;
                            $reasons = [];

                            foreach ($records as $record) {
                                // Verifica autorizzazione tramite policy
                                if ( ! auth()->user()->can('delete', $record)) {
                                    $skipped++;

                                    // Determina il motivo del blocco
                                    if ($record->status === \App\Enums\DinnerAvailabilityStatus::COMPLETED) {
                                        $reasons[] = "Disponibilità del {$record->dinnerDate->dinner_date->format('d/m/Y')}: completata (dato storico)";
                                    } elseif ($record->bookings()->exists()) {
                                        $reasons[] = "Disponibilità del {$record->dinnerDate->dinner_date->format('d/m/Y')}: ha prenotazioni collegate";
                                    } else {
                                        $reasons[] = "Disponibilità del {$record->dinnerDate->dinner_date->format('d/m/Y')}: non autorizzato";
                                    }

                                    continue;
                                }

                                $record->delete();
                                $deleted++;
                            }

                            // Notifiche
                            if ($deleted > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Disponibilità eliminate')
                                    ->body("{$deleted} disponibilità eliminate con successo.")
                                    ->send();
                            }

                            if ($skipped > 0) {
                                Notification::make()
                                    ->warning()
                                    ->title('Alcune disponibilità non eliminate')
                                    ->body("{$skipped} disponibilità saltate. Motivi:\n" . implode("\n", array_slice($reasons, 0, 3)))
                                    ->persistent()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('Nessuna Disponibilità per ospitare o essere ospitato')
            ->emptyStateDescription('Non hai ancora indicato una disponibilità. Aggiungi una data!')
            ->emptyStateIcon('tabler-calendar-off');
    }
}
