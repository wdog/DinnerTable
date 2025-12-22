<?php

namespace App\Observers;

use App\Models\DinnerBooking;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

class DinnerBookingObserver
{
    /**
     * Handle the DinnerBooking "created" event.
     * Quando viene creata una prenotazione, aggiorna lo status dell'host.
     */
    public function created(DinnerBooking $dinnerBooking): void
    {
        if ($dinnerBooking->status === 'confirmed') {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
        }
    }

    /**
     * Handle the DinnerBooking "updated" event.
     * Quando lo status di una prenotazione cambia o il numero di ospiti, aggiorna lo status dell'host.
     */
    public function updated(DinnerBooking $dinnerBooking): void
    {
        // Se lo status è cambiato o è cambiato il numero di ospiti
        if ($dinnerBooking->wasChanged('status') || $dinnerBooking->wasChanged('guests_count')) {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
        }
    }

    /**
     * Handle the DinnerBooking "deleted" event.
     * Quando viene eliminata una prenotazione, ripristina lo status dell'host.
     */
    public function deleted(DinnerBooking $dinnerBooking): void
    {
        $this->updateHostStatus($dinnerBooking->hostAvailability);
    }

    /**
     * Aggiorna lo status dell'host in base alle prenotazioni.
     */
    protected function updateHostStatus(DinnerAvailability $hostAvailability): void
    {
        // Solo se può ospitare
        if ( ! $hostAvailability->can_host) {
            return;
        }

        // Non modificare se è stato cancellato dall'host manualmente
        if ($hostAvailability->status === DinnerAvailabilityStatus::HOST_CANCELLED) {
            return;
        }

        $totalBookedGuests = $hostAvailability->total_booked_guests;
        $maxGuests         = $hostAvailability->max_guests ?? 0;

        $newStatus = match (true) {
            $totalBookedGuests >= $maxGuests => DinnerAvailabilityStatus::FULL,
            $totalBookedGuests > 0           => DinnerAvailabilityStatus::ALMOST_FULL,
            default                          => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        };

        // Usa saveQuietly per evitare loop infiniti
        if ($hostAvailability->status !== $newStatus) {
            $hostAvailability->status = $newStatus;
            $hostAvailability->saveQuietly();
        }
    }
}
