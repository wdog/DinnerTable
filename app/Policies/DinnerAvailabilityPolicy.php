<?php

namespace App\Policies;

use App\Models\DinnerAvailability;
use App\Models\User;

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
        return $user->id === $dinnerAvailability->user_id;;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        // TODO eliminare se non ci sono prenotazioni per la disponibilità
        return $user->id === $dinnerAvailability->user_id;;
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
