<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Enums\DinnerBookingStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\RelationManagers\RelationManager;

/**
 * Relation Manager per visualizzare le prenotazioni ricevute da un host.
 *
 * L'host può solo VISUALIZZARE le prenotazioni ricevute.
 * I guest gestiscono autonomamente le loro prenotazioni
 * (conferma/annulla dalla loro area).
 *
 * Permette all'host di:
 * - Visualizzare tutte le prenotazioni per la sua disponibilità
 * - Vedere chi ha prenotato, quanti ospiti porta, cosa porta
 * - Vedere lo stato della prenotazione (pending/confirmed/cancelled)
 * - Filtrare per stato
 *
 * Visibile solo quando:
 * - L'utente è host (can_host = true)
 *
 * @see DinnerAvailabilityResource
 * @see DinnerBooking
 */
class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'Prenotazioni ricevute';

    protected static ?string $modelLabel = 'prenotazione';

    protected static ?string $pluralModelLabel = 'prenotazioni';

    /**
     * Determina se il relation manager è visibile.
     * Mostra solo per host con prenotazioni.
     */
    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->can_host === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.name')
                    ->label('Ospite')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guests_count')
                    ->label('N. Ospiti')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('bringing_items')
                    ->label('Porta')
                    ->badge()
                    ->separator(',')
                    ->default('Niente')
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Note')
                    ->limit(50)
                    ->placeholder('Nessuna nota')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Prenotato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(DinnerBookingStatus::class)
                    ->native(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                // L'host non può creare prenotazioni
            ])
            ->recordActions([
                // Solo azione per visualizzare i dettagli
                Action::make('view')
                    ->label('Dettagli')
                    ->icon('tabler-eye')
                    ->color('gray')
                    ->modalHeading(fn ($record) => "Prenotazione di {$record->guest->name}")
                    ->modalContent(fn ($record) => view('filament.app.modals.booking-details', ['booking' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Chiudi'),
            ])

            ->emptyStateHeading('Nessuna prenotazione')
            ->emptyStateDescription('Non ci sono ancora prenotazioni per questa disponibilità.')
            ->emptyStateIcon('tabler-calendar-off');
    }
}
