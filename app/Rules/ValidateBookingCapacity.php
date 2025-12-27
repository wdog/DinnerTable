<?php

namespace App\Rules;

use Closure;
use App\Models\DinnerAvailability;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regola di validazione per verificare la capacità disponibile presso un host.
 *
 * Questa regola di validazione custom controlla che il numero di ospiti
 * richiesto in una prenotazione non superi la capacità massima dell'host,
 * considerando le prenotazioni già confermate.
 *
 * Funzionalità:
 * - Verifica che l'host esista e possa ospitare (can_host = true)
 * - Controlla che sia stato specificato un max_guests
 * - Calcola il totale di ospiti già prenotati (escludendo prenotazione in modifica)
 * - Valida che ci sia abbastanza spazio per i nuovi ospiti
 *
 * Uso tipico:
 * - Nel form di creazione prenotazione per validare guests_count
 * - Nel form di modifica prenotazione quando si cambia il numero di ospiti
 *
 * @see DinnerAvailability::confirmedBookings()
 * @see DinnerBooking::total_guests
 */
class ValidateBookingCapacity implements ValidationRule
{
    /**
     * Crea una nuova istanza della regola di validazione.
     *
     * @param  int  $hostAvailabilityId  ID della disponibilità host da validare
     * @param  int|null  $excludeBookingId  ID della prenotazione da escludere dal conteggio (in caso di modifica)
     */
    public function __construct(
        protected int $hostAvailabilityId,
        protected ?int $excludeBookingId = null
    ) {}

    /**
     * Esegue la validazione della capacità disponibile.
     *
     * Il metodo verifica che ci siano abbastanza posti disponibili
     * presso l'host per il numero di ospiti richiesto.
     *
     * Passi della validazione:
     * 1. Verifica esistenza disponibilità host
     * 2. Verifica che sia di tipo host (can_host = true)
     * 3. Verifica che sia specificato max_guests
     * 4. Calcola totale ospiti già prenotati (escludendo booking in modifica)
     * 5. Verifica che totale + richiesti <= max_guests
     *
     * @param  string  $attribute  Nome del campo (es. 'guests_count')
     * @param  mixed  $value  Valore del campo (numero ospiti richiesti)
     * @param  Closure  $fail  Closure per segnalare errore di validazione
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hostAvailability = DinnerAvailability::find($this->hostAvailabilityId);

        if ( ! $hostAvailability) {
            $fail("La disponibilità dell'host non esiste.", null);

            return;
        }

        if ( ! $hostAvailability->can_host) {
            $fail('Questa disponibilità non può ospitare.', null);

            return;
        }

        if ( ! $hostAvailability->max_guests) {
            $fail("L'host non ha specificato il numero massimo di ospiti.", null);

            return;
        }

        // Calcola il totale degli ospiti già prenotati (escludendo eventualmente questa prenotazione in modifica)
        $totalBookedGuests = $hostAvailability->confirmedBookings()
            ->when($this->excludeBookingId, function ($query) {
                $query->where('id', '!=', $this->excludeBookingId);
            })
            ->get()
            ->sum(function ($booking) {
                return $booking->total_guests;
            });

        // Calcola il totale con questa nuova prenotazione
        // $value è guests_count
        $requestedGuests     = ((int) $value);
        $totalWithNewBooking = $totalBookedGuests + $requestedGuests;

        if ($totalWithNewBooking > $hostAvailability->max_guests) {
            $availableSpots = $hostAvailability->max_guests - $totalBookedGuests;
            $fail("Non ci sono abbastanza posti disponibili. Posti disponibili: {$availableSpots}, richiesti: {$requestedGuests}.", null);
        }
    }
}
