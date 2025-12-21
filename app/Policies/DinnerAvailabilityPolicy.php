<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DinnerAvailability;

class DinnerAvailabilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return $user->dinner_group_id === $dinnerAvailability->dinnerDate->dinner_group_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return $user->id === $dinnerAvailability->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        // Può eliminare solo se è il proprietario
        if ($user->id !== $dinnerAvailability->user_id) {
            return false;
        }

        // Non può eliminare se ci sono prenotazioni confermate
        $hasConfirmedBookings = $dinnerAvailability->bookings()
            ->where('status', 'confirmed')
            ->exists();

        return ! $hasConfirmedBookings;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return false;
    }
}
