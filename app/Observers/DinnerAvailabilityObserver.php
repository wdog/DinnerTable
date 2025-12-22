<?php

namespace App\Observers;

use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Log;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DinnerCancelledByHostNotification;

/**
 * Observer per il modello DinnerAvailability.
 *
 * Gestisce gli eventi del ciclo di vita delle disponibilità,
 * in particolare la cancellazione da parte dell'host e le
 * conseguenze sulle prenotazioni degli ospiti.
 *
 * Funzionalità principali:
 * - Cancellazione automatica prenotazioni quando host cancella
 * - Notifica agli ospiti della cancellazione
 * - Logging delle operazioni critiche
 *
 * @see DinnerAvailability
 * @see DinnerCancelledByHostNotification
 */
class DinnerAvailabilityObserver
{
    /**
     * Handle the DinnerAvailability "updated" event.
     *
     * Intercetta il cambio di stato della disponibilità.
     * Quando un host imposta lo stato su HOST_CANCELLED:
     * 1. Cancella tutte le prenotazioni confermate
     * 2. Invia notifiche a tutti gli ospiti interessati
     * 3. Registra l'operazione nei log
     *
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità aggiornata
     */
    public function updated(DinnerAvailability $dinnerAvailability): void
    {
        // Verifica se lo stato è cambiato a HOST_CANCELLED
        if (
            $dinnerAvailability->wasChanged('status') &&
            $dinnerAvailability->status === DinnerAvailabilityStatus::HOST_CANCELLED &&
            $dinnerAvailability->can_host
        ) {
            $this->handleHostCancellation($dinnerAvailability);
        }
    }

    /**
     * Gestisce la cancellazione della cena da parte dell'host.
     *
     * Cancella tutte le prenotazioni confermate e notifica
     * gli ospiti interessati della cancellazione.
     *
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità cancellata dall'host
     */
    protected function handleHostCancellation(DinnerAvailability $dinnerAvailability): void
    {
        // Ottieni tutte le prenotazioni confermate
        $confirmedBookings = $dinnerAvailability->confirmedBookings()->with('guest')->get();

        if ($confirmedBookings->isEmpty()) {
            Log::info('Host cancelled dinner with no confirmed bookings', [
                'availability_id' => $dinnerAvailability->id,
                'host_id'         => $dinnerAvailability->user_id,
                'dinner_date'     => $dinnerAvailability->dinnerDate->dinner_date,
            ]);

            return;
        }

        $cancelledCount = 0;
        $notifiedGuests = [];

        // Cancella tutte le prenotazioni e notifica gli ospiti
        foreach ($confirmedBookings as $booking) {
            // Cambia lo stato della prenotazione a 'cancelled'
            $booking->status = 'cancelled';
            $booking->saveQuietly(); // Usa saveQuietly per evitare loop con DinnerBookingObserver

            $cancelledCount++;
            Log::debug("Cancelled booking: {$booking->id}");
            // Invia notifica all'ospite
            try {
                Notification::send(
                    $booking->guest,
                    new DinnerCancelledByHostNotification($dinnerAvailability, $booking)
                );

                $notifiedGuests[] = $booking->guest->id;
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation notification', [
                    'guest_id'   => $booking->guest->id,
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        // Log dell'operazione
        Log::info('Host cancelled dinner and bookings were cancelled', [
            'availability_id'     => $dinnerAvailability->id,
            'host_id'             => $dinnerAvailability->user_id,
            'dinner_date'         => $dinnerAvailability->dinnerDate->dinner_date,
            'cancelled_bookings'  => $cancelledCount,
            'notified_guests'     => $notifiedGuests,
            'cancellation_reason' => $dinnerAvailability->cancellation_reason ?? 'Nessun motivo specificato',
        ]);
    }
}
