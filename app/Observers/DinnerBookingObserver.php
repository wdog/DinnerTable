<?php

namespace App\Observers;

use App\Models\DinnerBooking;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

/**
 * Observer per il modello DinnerBooking.
 *
 * Gestisce l'aggiornamento automatico dello stato della disponibilità host
 * in risposta alle modifiche delle prenotazioni.
 *
 * Responsabilità principale:
 * - Mantenere sincronizzato lo stato della disponibilità (AVAILABLE_TO_HOST,
 *   ALMOST_FULL, FULL) in base al numero totale di ospiti prenotati
 *
 * Eventi gestiti:
 * - created: nuova prenotazione confermata
 * - updated: cambio status prenotazione o numero ospiti
 * - deleted: eliminazione prenotazione
 *
 * Logica di calcolo stato:
 * - FULL: total_booked_guests >= max_guests
 * - ALMOST_FULL: total_booked_guests > 0 ma < max_guests
 * - AVAILABLE_TO_HOST: nessun ospite prenotato (total_booked_guests = 0)
 *
 * Protezioni:
 * - Non modifica disponibilità guest (can_host = false)
 * - Non sovrascrive stato HOST_CANCELLED impostato manualmente
 * - Usa saveQuietly() per evitare loop con DinnerAvailabilityObserver
 *
 * Registrazione observer:
 * L'observer viene registrato automaticamente tramite attributo
 * #[ObservedBy(DinnerBookingObserver::class)] sul modello.
 *
 * @see \App\Models\DinnerBooking
 * @see \App\Models\DinnerAvailability
 * @see \App\Enums\DinnerAvailabilityStatus
 * @see \App\Observers\DinnerAvailabilityObserver
 */
class DinnerBookingObserver
{
    /**
     * Gestisce l'evento "created" del modello DinnerBooking.
     *
     * Quando viene creata una nuova prenotazione, se il suo stato è
     * 'confirmed', aggiorna automaticamente lo stato della disponibilità
     * host per riflettere il nuovo numero di ospiti.
     *
     * Esempio flusso:
     * 1. Guest prenota 2 posti
     * 2. Booking creato con status='confirmed', guests_count=2
     * 3. Observer calcola nuovo total_booked_guests
     * 4. Aggiorna stato host (es. AVAILABLE_TO_HOST -> ALMOST_FULL)
     *
     * @param DinnerBooking $dinnerBooking Prenotazione appena creata
     * @return void
     */
    public function created(DinnerBooking $dinnerBooking): void
    {
        // Aggiorna solo se la prenotazione è confermata
        if ($dinnerBooking->status === 'confirmed') {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
        }
    }

    /**
     * Gestisce l'evento "updated" del modello DinnerBooking.
     *
     * Aggiorna lo stato host quando cambiano campi rilevanti:
     * - status: prenotazione confermata/cancellata
     * - guests_count: numero ospiti modificato
     *
     * Esempi di scenari:
     * - Guest aumenta da 2 a 4 ospiti -> possibile passaggio a FULL
     * - Prenotazione cancellata (status='cancelled') -> posti liberati
     * - Guest riduce da 3 a 1 ospiti -> possibile ritorno a AVAILABLE_TO_HOST
     *
     * @param DinnerBooking $dinnerBooking Prenotazione modificata
     * @return void
     */
    public function updated(DinnerBooking $dinnerBooking): void
    {
        // Aggiorna solo se è cambiato lo status o il numero di ospiti
        if ($dinnerBooking->wasChanged('status') || $dinnerBooking->wasChanged('guests_count')) {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
        }
    }

    /**
     * Gestisce l'evento "deleted" del modello DinnerBooking.
     *
     * Quando una prenotazione viene eliminata, aggiorna lo stato host
     * per riflettere la liberazione dei posti.
     *
     * Esempio:
     * - Host aveva 4/4 posti (FULL)
     * - Guest elimina prenotazione di 2 posti
     * - Observer aggiorna a 2/4 (ALMOST_FULL)
     *
     * @param DinnerBooking $dinnerBooking Prenotazione eliminata
     * @return void
     */
    public function deleted(DinnerBooking $dinnerBooking): void
    {
        $this->updateHostStatus($dinnerBooking->hostAvailability);
    }

    /**
     * Calcola e aggiorna lo stato della disponibilità host.
     *
     * Logica di aggiornamento:
     *
     * 1. **Validazioni preliminari**:
     *    - Verifica can_host = true (solo disponibilità host)
     *    - Salta se stato è HOST_CANCELLED (decisione manuale dell'host)
     *
     * 2. **Calcolo nuovo stato** (tramite match expression):
     *    - FULL: se total_booked_guests >= max_guests
     *    - ALMOST_FULL: se total_booked_guests > 0 (ma non pieno)
     *    - AVAILABLE_TO_HOST: se total_booked_guests = 0 (nessuna prenotazione)
     *
     * 3. **Salvataggio**:
     *    - Confronta con stato attuale per evitare update inutili
     *    - Usa saveQuietly() per prevenire loop infiniti con observer disponibilità
     *
     * Note tecniche:
     * - total_booked_guests è un attributo calcolato sul modello DinnerAvailability
     *   che somma i guests_count di tutte le prenotazioni confermate
     * - saveQuietly() bypassa gli event observers, evitando che
     *   DinnerAvailabilityObserver venga triggerato
     *
     * Stati preservati:
     * - HOST_CANCELLED: non viene mai sovrascritto automaticamente
     * - COMPLETED: non viene mai sovrascritto automaticamente (impostato da cron)
     *
     * @param DinnerAvailability $hostAvailability Disponibilità host da aggiornare
     * @return void
     */
    protected function updateHostStatus(DinnerAvailability $hostAvailability): void
    {
        // Guard: solo disponibilità host possono avere booking status aggiornato
        if ( ! $hostAvailability->can_host) {
            return;
        }

        // Guard: non sovrascrivere cancellazioni manuali dell'host
        if ($hostAvailability->status === DinnerAvailabilityStatus::HOST_CANCELLED) {
            return;
        }

        // Calcola totale ospiti prenotati (somma guests_count delle prenotazioni confermate)
        $totalBookedGuests = $hostAvailability->total_booked_guests;
        $maxGuests         = $hostAvailability->max_guests ?? 0;

        // Determina nuovo stato basato su occupazione
        $newStatus = match (true) {
            $totalBookedGuests >= $maxGuests => DinnerAvailabilityStatus::FULL,           // Pieno
            $totalBookedGuests > 0           => DinnerAvailabilityStatus::ALMOST_FULL,    // Parzialmente prenotato
            default                          => DinnerAvailabilityStatus::AVAILABLE_TO_HOST, // Libero
        };

        // Aggiorna solo se lo stato è effettivamente cambiato
        if ($hostAvailability->status !== $newStatus) {
            $hostAvailability->status = $newStatus;

            // IMPORTANTE: saveQuietly() per evitare loop con DinnerAvailabilityObserver
            $hostAvailability->saveQuietly();
        }
    }
}
