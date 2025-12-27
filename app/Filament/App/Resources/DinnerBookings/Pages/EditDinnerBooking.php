<?php

namespace App\Filament\App\Resources\DinnerBookings\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\App\Resources\DinnerBookings\DinnerBookingResource;

/**
 * Pagina Filament per modificare una prenotazione esistente.
 *
 * Questa pagina permette ai guest di modificare le proprie prenotazioni
 * per una cena, con controlli automatici basati sullo stato della
 * disponibilità dell'host.
 *
 * Funzionalità principali:
 * - Modifica numero di ospiti (guests_count)
 * - Modifica note della prenotazione
 * - Cambio stato tramite campo select nel form (non eliminazione fisica)
 * - Disabilitazione automatica form per stati non modificabili
 *
 * Controlli di sicurezza:
 * - Form visibile solo se lo stato host permette modifiche
 * - Validazioni tramite policy DinnerBookingPolicy
 * - Controllo tramite hostAvailability->status->canUpdateBookings()
 * - Policy blocca eliminazione fisica (delete sempre false)
 *
 * Stati che bloccano le modifiche:
 * - COMPLETED: cena già avvenuta
 * - HOST_CANCELLED: host ha cancellato la disponibilità
 * - CANCELLED: prenotazione già cancellata (read-only)
 *
 * **Cancellazione prenotazione**:
 * - NON esiste pulsante "Elimina" (hard delete bloccato da policy)
 * - Per cancellare: cambiare campo 'status' a CANCELLED nel form
 * - L'Observer gestisce automaticamente liberazione posti e stati
 *
 * @see DinnerBookingResource Risorsa principale
 * @see \App\Models\DinnerBooking Modello prenotazione
 * @see \App\Enums\DinnerAvailabilityStatus::canUpdateBookings()
 * @see \App\Policies\DinnerBookingPolicy::delete() Sempre false
 * @see \App\Observers\DinnerBookingObserver Gestisce cambio stato
 */
class EditDinnerBooking extends EditRecord
{
    /**
     * Risorsa Filament associata a questa pagina.
     */
    protected static string $resource = DinnerBookingResource::class;

    /**
     * Definisce le azioni disponibili nell'header della pagina.
     *
     * Nessuna azione header in quanto:
     * - L'eliminazione fisica è bloccata dalla policy (delete() = false)
     * - La cancellazione avviene tramite cambio campo 'status' nel form
     * - Non servono altre azioni particolari nell'header
     *
     * @return array Array vuoto - nessuna azione header
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Definisce le azioni disponibili nel form di modifica.
     *
     * Controlla se la prenotazione può essere modificata verificando
     * lo stato della disponibilità host e della prenotazione stessa.
     *
     * Nasconde i pulsanti (Salva, Annulla) se:
     * - Disponibilità host in stato COMPLETED o HOST_CANCELLED
     * - Prenotazione in stato CANCELLED
     * - Data nel passato
     *
     * Logica:
     * - Se isReadOnly() = true -> nessun pulsante
     * - Altrimenti -> mostra i pulsanti standard del parent (Salva, Annulla)
     *
     * @return array<\Filament\Actions\Action> Array di azioni form (vuoto o standard)
     *
     * @see \App\Enums\DinnerAvailabilityStatus::canUpdateBookings()
     */
    protected function getFormActions(): array
    {
        if ($this->isReadOnly()) {
            return [];
        }

        return parent::getFormActions();
    }

    /**
     * Verifica se la prenotazione è in sola lettura.
     *
     * Condizioni per read-only:
     * - Stato disponibilità host non permette modifiche (COMPLETED, HOST_CANCELLED)
     * - Prenotazione in stato CANCELLED
     * - Data nel passato
     */
    protected function isReadOnly(): bool
    {
        if ( ! $this->record) {
            return false;
        }

        // Prenotazione cancellata = read-only
        if ($this->record->status->value === 'cancelled') {
            return false;
        }

        // Disponibilità host non permette modifiche
        if ( ! $this->record->hostAvailability->status->canUpdateBookings()) {
            return true;
        }

        // Data passata = read-only
        if (
            $this->record->hostAvailability->dinnerDate &&
            $this->record->hostAvailability->dinnerDate->dinner_date < today()
        ) {
            return true;
        }

        return false;
    }
}
