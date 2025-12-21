<?php

namespace App\Observers;

use App\Models\DinnerBooking;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

class DinnerBookingObserver
{
    /**
     * Handle the DinnerBooking "created" event.
     * Quando viene creata una prenotazione, aggiorna lo status dell'host e del guest.
     */
    public function created(DinnerBooking $dinnerBooking): void
    {
        if ($dinnerBooking->status === 'confirmed') {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
            $this->updateGuestStatus($dinnerBooking);
        }
    }

    /**
     * Handle the DinnerBooking "updated" event.
     * Quando lo status di una prenotazione cambia, aggiorna gli status.
     */
    public function updated(DinnerBooking $dinnerBooking): void
    {
        // Se lo status è cambiato
        if ($dinnerBooking->wasChanged('status')) {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
            $this->updateGuestStatus($dinnerBooking);
        }

        // Se è cambiato il numero di ospiti, aggiorna lo status dell'host
        if ($dinnerBooking->wasChanged('guests_count')) {
            $this->updateHostStatus($dinnerBooking->hostAvailability);
        }
    }

    /**
     * Handle the DinnerBooking "deleted" event.
     * Quando viene eliminata una prenotazione, ripristina gli status.
     */
    public function deleted(DinnerBooking $dinnerBooking): void
    {
        $this->updateHostStatus($dinnerBooking->hostAvailability);
        $this->updateGuestStatus($dinnerBooking);
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
        $maxGuests = $hostAvailability->max_guests ?? 0;

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

    /**
     * Aggiorna lo status del guest in base alle sue prenotazioni.
     */
    protected function updateGuestStatus(DinnerBooking $dinnerBooking): void
    {
        $guest = $dinnerBooking->guest;
        $dinnerDate = $dinnerBooking->hostAvailability->dinnerDate;

        // Trova la disponibilità del guest per quella data
        $guestAvailability = $guest->availabilities()
            ->where('dinner_date_id', $dinnerDate->id)
            ->first();

        if ( ! $guestAvailability) {
            return;
        }

        // Non aggiornare se l'utente può ospitare (è un host)
        if ($guestAvailability->can_host) {
            return;
        }

        // Verifica se ha una prenotazione confermata per quella data
        $hasConfirmedBooking = $guest->guestBookings()
            ->where('status', 'confirmed')
            ->whereHas('hostAvailability', function ($query) use ($dinnerDate) {
                $query->where('dinner_date_id', $dinnerDate->id);
            })
            ->exists();

        $newStatus = $hasConfirmedBooking
            ? DinnerAvailabilityStatus::BOOKED
            : DinnerAvailabilityStatus::AVAILABLE;

        // Usa saveQuietly per evitare loop infiniti
        if ($guestAvailability->status !== $newStatus) {
            $guestAvailability->status = $newStatus;
            $guestAvailability->saveQuietly();
        }
    }
}
