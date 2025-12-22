<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\RelationManagers;

use Filament\Tables\Table;
use App\Enums\DinnerBookingStatus;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

/**
 * Relation Manager per gestire le prenotazioni ricevute da un host.
 *
 * Permette all'host di:
 * - Visualizzare tutte le prenotazioni per la sua disponibilità
 * - Vedere chi ha prenotato, quanti ospiti porta, cosa porta
 * - Confermare o annullare prenotazioni
 * - Filtrare per stato (in attesa, confermato, cancellato)
 *
 * Visibile solo quando:
 * - L'utente è host (can_host = true)
 * - Ci sono prenotazioni da mostrare
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
                TextColumn::make('guest.nome')
                    ->label('Ospite')
                    ->description(fn ($record) => $record->guest->cognome)
                    ->searchable(['nome', 'cognome'])
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
                // Non permettiamo di creare prenotazioni da qui
            ])
            ->recordActions([
                // Azione per confermare la prenotazione
                Action::make('confirm')
                    ->label('Conferma')
                    ->icon('tabler-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === DinnerBookingStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Conferma prenotazione')
                    ->modalDescription(fn ($record) => "Confermare la prenotazione di {$record->guest->nome} {$record->guest->cognome}?")
                    ->action(function ($record) {
                        $record->status = DinnerBookingStatus::CONFIRMED;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Prenotazione confermata')
                            ->body("La prenotazione di {$record->guest->nome} è stata confermata.")
                            ->send();
                    }),

                // Azione per annullare la prenotazione
                Action::make('cancel')
                    ->label('Annulla')
                    ->icon('tabler-x')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== DinnerBookingStatus::CANCELLED)
                    ->requiresConfirmation()
                    ->modalHeading('Annulla prenotazione')
                    ->modalDescription(fn ($record) => "Sei sicuro di voler annullare la prenotazione di {$record->guest->nome} {$record->guest->cognome}?")
                    ->action(function ($record) {
                        $record->status = DinnerBookingStatus::CANCELLED;
                        $record->save();

                        Notification::make()
                            ->warning()
                            ->title('Prenotazione annullata')
                            ->body("La prenotazione di {$record->guest->nome} è stata annullata.")
                            ->send();
                    }),

                // Azione per visualizzare i dettagli
                Action::make('view')
                    ->label('Dettagli')
                    ->icon('tabler-eye')
                    ->color('gray')
                    ->modalHeading(fn ($record) => "Prenotazione di {$record->guest->nome} {$record->guest->cognome}")
                    ->modalContent(fn ($record) => view('filament.app.modals.booking-details', ['booking' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Chiudi'),
            ])
            ->actions([
                // Azione bulk per confermare più prenotazioni
                BulkAction::make('confirm_all')
                    ->label('Conferma selezionate')
                    ->icon('tabler-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->status === DinnerBookingStatus::PENDING) {
                                $record->status = DinnerBookingStatus::CONFIRMED;
                                $record->save();
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Prenotazioni confermate')
                            ->body("{$count} prenotazioni sono state confermate.")
                            ->send();
                    }),

                DeleteBulkAction::make()
                    ->label('Elimina selezionate'),
            ])
            ->emptyStateHeading('Nessuna prenotazione')
            ->emptyStateDescription('Non ci sono ancora prenotazioni per questa disponibilità.')
            ->emptyStateIcon('tabler-calendar-off');
    }
}
