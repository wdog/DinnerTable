<?php

namespace App\Rules;

use Closure;
use App\Models\DinnerAvailability;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateBookingCapacity implements ValidationRule
{
    public function __construct(
        protected int $hostAvailabilityId,
        protected ?int $excludeBookingId = null
    ) {}

    /**
     * Run the validation rule.
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
