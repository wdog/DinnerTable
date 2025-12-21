<?php

namespace App\Policies;

use App\Enums\DinnerAvailabilityStatus;
use App\Models\DinnerAvailability;
use App\Models\DinnerBooking;
use App\Models\User;
use Carbon\Carbon;

class DinnerBookingPolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi prenotazione.
     */
    public function viewAny(User $user): bool
    {
        return $user->dinner_group_id !== null;
    }

    /**
     * Determina se l'utente può visualizzare una prenotazione.
     */
    public function view(User $user, DinnerBooking $booking): bool
    {
        // Può vedere se è il guest o l'host della disponibilità
        return $booking->guest_user_id === $user->id
            || $booking->hostAvailability->user_id === $user->id;
    }

    /**
     * Determina se l'utente può creare una prenotazione.
     */
    public function create(User $user): bool
    {
        return $user->dinner_group_id !== null;
    }

    /**
     * Determina se l'utente può prenotare una specifica disponibilità.
     * Questo metodo viene usato per validare la logica di business della prenotazione.
     */
    public function book(User $user, DinnerAvailability $availability): bool
    {
        // 1. L'utente deve appartenere a un gruppo
        if ($user->dinner_group_id === null) {
            return false;
        }

        // 2. Non può prenotare la propria disponibilità
        if ($availability->user_id === $user->id) {
            return false;
        }

        // 3. Deve essere dello stesso gruppo
        if ($availability->dinnerDate->dinner_group_id !== $user->dinner_group_id) {
            return false;
        }

        // 4. La disponibilità deve essere di tipo host
        if (! $availability->can_host) {
            return false;
        }

        // 5. Lo stato deve permettere prenotazioni (AVAILABLE_TO_HOST o ALMOST_FULL)
        if (! $availability->status->canAcceptBookings()) {
            return false;
        }

        // 6. Devono esserci posti disponibili
        if (! $availability->hasAvailableSpots()) {
            return false;
        }

        // 7. Non deve aver già prenotato questa disponibilità
        $hasAlreadyBooked = DinnerBooking::where('host_availability_id', $availability->id)
            ->where('guest_user_id', $user->id)
            ->where('status', 'confirmed')
            ->exists();

        if ($hasAlreadyBooked) {
            return false;
        }

        // 8. Non deve avere altre prenotazioni confermate nello stesso giorno
        $dinnerDate = $availability->dinnerDate->dinner_date;
        $hasOtherBookingsOnSameDay = DinnerBooking::where('guest_user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereHas('hostAvailability.dinnerDate', function ($query) use ($dinnerDate) {
                $query->where('dinner_date', $dinnerDate);
            })
            ->exists();

        if ($hasOtherBookingsOnSameDay) {
            return false;
        }

        return true;
    }

    /**
     * Determina se l'utente può aggiornare una prenotazione.
     */
    public function update(User $user, DinnerBooking $booking): bool
    {
        // Solo il guest può modificare la propria prenotazione
        return $booking->guest_user_id === $user->id;
    }

    /**
     * Determina se l'utente può cancellare una prenotazione.
     */
    public function delete(User $user, DinnerBooking $booking): bool
    {
        // Sia il guest che l'host possono cancellare la prenotazione
        return $booking->guest_user_id === $user->id
            || $booking->hostAvailability->user_id === $user->id;
    }

    /**
     * Determina se l'utente può ripristinare una prenotazione.
     */
    public function restore(User $user, DinnerBooking $booking): bool
    {
        return $booking->guest_user_id === $user->id;
    }

    /**
     * Determina se l'utente può eliminare definitivamente una prenotazione.
     */
    public function forceDelete(User $user, DinnerBooking $booking): bool
    {
        // Solo admin o il guest possono eliminare definitivamente
        return $user->hasRole('super_admin') || $booking->guest_user_id === $user->id;
    }
}
