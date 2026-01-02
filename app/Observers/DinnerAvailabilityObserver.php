<?php

namespace App\Observers;

use App\Models\DinnerLog;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
 * Flusso cancellazione host:
 * 1. Host cambia stato disponibilità a HOST_CANCELLED
 * 2. Observer intercetta il cambio (evento "updated")
 * 3. Tutte le prenotazioni confermate vengono cancellate
 * 4. Ogni guest riceve una notifica via database
 * 5. Operazione loggata per audit trail
 *
 * Note tecniche:
 * - Usa saveQuietly() per evitare loop con DinnerBookingObserver
 * - Gestione errori con try/catch per notifiche
 * - Logging dettagliato per troubleshooting
 *
 * Registrazione observer:
 * L'observer viene registrato automaticamente tramite attributo
 * #[ObservedBy(DinnerAvailabilityObserver::class)] sul modello.
 *
 * @see \App\Models\DinnerAvailability
 * @see \App\Notifications\DinnerCancelledByHostNotification
 * @see \App\Observers\DinnerBookingObserver
 */
class DinnerAvailabilityObserver
{
    /**
     * Gestisce l'evento "created" del modello DinnerAvailability.
     *
     * Crea un log entry quando viene creata una nuova disponibilità,
     * tracciando l'utente che l'ha creata e i dati iniziali.
     *
     * @param  DinnerAvailability  $availability  Disponibilità appena creata
     */
    public function created(DinnerAvailability $availability): void
    {
        DinnerLog::logEvent(
            availability: $availability,
            status: $availability->status->value,
            userId: $availability->user_id,
            metadata: [
                'event'      => 'created',
                'max_guests' => $availability->max_guests,
            ]
        );
    }

    /**
     * Gestisce l'evento "updated" del modello DinnerAvailability.
     *
     * Intercetta il cambio di stato della disponibilità e attiva
     * la logica di cancellazione quando necessario.
     *
     * Condizioni per attivare cancellazione automatica:
     * - Campo 'status' deve essere cambiato (wasChanged)
     * - Nuovo stato deve essere HOST_CANCELLED
     * - Disponibilità deve essere di tipo host (can_host = true)
     *
     * Se tutte le condizioni sono soddisfatte, chiama handleHostCancellation()
     * che gestisce la cancellazione delle prenotazioni e notifiche.
     *
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità aggiornata
     *
     * @see handleHostCancellation()
     */
    public function updated(DinnerAvailability $dinnerAvailability): void
    {
        // Log cambio status se avvenuto
        if ($dinnerAvailability->wasChanged('status')) {
            $oldStatus = $dinnerAvailability->getOriginal('status');
            $newStatus = $dinnerAvailability->status->value;

            DinnerLog::logEvent(
                availability: $dinnerAvailability,
                status: $newStatus,
                userId: Auth::id(), // NULLABLE - null per eventi di sistema
                metadata: [
                    'event'               => 'status_changed',
                    'old_status'          => $oldStatus,
                    'new_status'          => $newStatus,
                    'cancellation_reason' => $dinnerAvailability->cancellation_reason?->value,
                ]
            );
        }

        // Log cambio dinner_name
        if ($dinnerAvailability->wasChanged('dinner_name')) {
            DinnerLog::logEvent(
                availability: $dinnerAvailability,
                status: $dinnerAvailability->status->value,
                userId: Auth::id(),
                metadata: [
                    'event'     => 'dinner_name_changed',
                    'old_value' => $dinnerAvailability->getOriginal('dinner_name'),
                    'new_value' => $dinnerAvailability->dinner_name,
                ]
            );
        }

        // Log cambio max_guests
        if ($dinnerAvailability->wasChanged('max_guests')) {
            DinnerLog::logEvent(
                availability: $dinnerAvailability,
                status: $dinnerAvailability->status->value,
                userId: Auth::id(),
                metadata: [
                    'event'     => 'max_guests_changed',
                    'old_value' => $dinnerAvailability->getOriginal('max_guests'),
                    'new_value' => $dinnerAvailability->max_guests,
                ]
            );
        }

        // Log cambio note
        if ($dinnerAvailability->wasChanged('note')) {
            DinnerLog::logEvent(
                availability: $dinnerAvailability,
                status: $dinnerAvailability->status->value,
                userId: Auth::id(),
                metadata: [
                    'event'     => 'note_changed',
                    'old_value' => $dinnerAvailability->getOriginal('note'),
                    'new_value' => $dinnerAvailability->note,
                ]
            );
        }

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
     * Questo metodo orchestr a il flusso completo di cancellazione:
     *
     * 1. **Recupero prenotazioni**: Ottiene tutte le prenotazioni confermate
     *    con eager loading dei guest per ottimizzare le query
     *
     * 2. **Early return**: Se non ci sono prenotazioni, logga e termina
     *
     * 3. **Cancellazione prenotazioni**: Per ogni prenotazione confermata:
     *    - Cambia status a 'cancelled'
     *    - Usa saveQuietly() per evitare trigger del DinnerBookingObserver
     *      (che altrimenti aggiornerebbe lo stato della disponibilità)
     *
     * 4. **Notifiche**: Invia notifica database a ogni guest con:
     *    - Dettagli della cena cancellata
     *    - Informazioni sull'host
     *    - Motivo della cancellazione (se fornito)
     *    - Gestione errori con try/catch per resilienza
     *
     * 5. **Logging**: Registra operazione completa per audit trail
     *
     * Nota tecnica - saveQuietly():
     * È fondamentale usare saveQuietly() invece di save() per evitare
     * loop infiniti. Altrimenti DinnerBookingObserver verrebbe triggerato
     * e tentereb be di aggiornare nuovamente lo stato della disponibilità.
     *
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità cancellata dall'host
     */
    protected function handleHostCancellation(DinnerAvailability $dinnerAvailability): void
    {
        // Ottieni tutte le prenotazioni confermate con eager loading dei guest
        $confirmedBookings = $dinnerAvailability->confirmedBookings()->with('guest')->get();

        // Early return se non ci sono prenotazioni da cancellare
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

        // Itera su tutte le prenotazioni confermate
        foreach ($confirmedBookings as $booking) {
            // Cambia lo stato della prenotazione a 'cancelled'
            $booking->status = 'cancelled';

            // IMPORTANTE: Usa saveQuietly() per evitare loop con DinnerBookingObserver
            // Se usassimo save(), l'observer della prenotazione verrebbe triggerato
            // e tenterebb e di aggiornare lo stato della disponibilità creando un loop
            $booking->saveQuietly();

            $cancelledCount++;
            Log::debug("Cancelled booking: {$booking->id}");

            // Invia notifica database all'ospite
            try {
                Notification::send(
                    $booking->guest,
                    new DinnerCancelledByHostNotification($dinnerAvailability, $booking)
                );

                $notifiedGuests[] = $booking->guest->id;
            } catch (\Exception $e) {
                // Log dell'errore ma continua con le altre notifiche
                Log::error('Failed to send cancellation notification', [
                    'guest_id'   => $booking->guest->id,
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        // Log finale dell'operazione completa per audit trail
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
