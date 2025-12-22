<?php

namespace App\Filament\App\Resources\DinnerBookings\Pages;

use Filament\Actions\DeleteAction;
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
 * - Cancellazione prenotazione tramite DeleteAction
 * - Disabilitazione automatica form per stati non modificabili
 *
 * Controlli di sicurezza:
 * - Form visibile solo se lo stato host permette modifiche
 * - Validazioni tramite policy DinnerBookingPolicy
 * - Controllo tramite hostAvailability->status->canUpdateBookings()
 *
 * Stati che bloccano le modifiche:
 * - COMPLETED: cena già avvenuta
 * - HOST_CANCELLED: host ha cancellato la disponibilità
 *
 * @see DinnerBookingResource Risorsa principale
 * @see \App\Models\DinnerBooking Modello prenotazione
 * @see \App\Enums\DinnerAvailabilityStatus::canUpdateBookings()
 * @see \App\Policies\DinnerBookingPolicy
 */
class EditDinnerBooking extends EditRecord
{
    /**
     * Risorsa Filament associata a questa pagina.
     *
     * @var string
     */
    protected static string $resource = DinnerBookingResource::class;

    /**
     * Definisce le azioni disponibili nell'header della pagina.
     *
     * Fornisce l'azione di cancellazione della prenotazione.
     * L'azione DeleteAction viene controllata dalla policy per
     * verificare i permessi dell'utente.
     *
     * @return array<DeleteAction> Array di azioni header
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Definisce le azioni disponibili nel form di modifica.
     *
     * Controlla se la prenotazione può essere modificata verificando
     * lo stato della disponibilità host. Se lo stato non permette
     * modifiche (es. COMPLETED o HOST_CANCELLED), nasconde completamente
     * i pulsanti del form (Salva, Annulla) rendendo il form di sola lettura.
     *
     * Logica:
     * - Se hostAvailability->status->canUpdateBookings() = false -> nessun pulsante
     * - Altrimenti -> mostra i pulsanti standard del parent (Salva, Annulla)
     *
     * Questo impedisce agli utenti di modificare prenotazioni per:
     * - Cene già concluse (COMPLETED)
     * - Disponibilità cancellate dall'host (HOST_CANCELLED)
     *
     * @return array<\Filament\Actions\Action> Array di azioni form (vuoto o standard)
     * @see \App\Enums\DinnerAvailabilityStatus::canUpdateBookings()
     */
    protected function getFormActions(): array
    {
        // Verifica se lo stato della disponibilità host permette modifiche
        if ( ! $this->record->hostAvailability->status->canUpdateBookings()) {
            // Nessuna azione disponibile = form in sola lettura
            return [];
        }

        // Altrimenti usa le azioni standard (Salva, Annulla)
        return parent::getFormActions();
    }
}
