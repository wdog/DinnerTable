<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\DinnerDate;
use App\Enums\CancellationReason;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per il modello DinnerAvailability.
 *
 * Genera disponibilità per le cene, sia come host che come guest,
 * con vari stati possibili (disponibile, pieno, cancellato, completato).
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DinnerAvailability>
 */
class DinnerAvailabilityFactory extends Factory
{
    /**
     * Definisce lo stato di default del model.
     *
     * Crea una disponibilità guest (can_host = false) con status AVAILABLE.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dinner_date_id'      => DinnerDate::factory(),
            'user_id'             => User::factory(),
            'status'              => DinnerAvailabilityStatus::AVAILABLE,
            'can_host'            => false,
            'dinner_name'         => null,
            'max_guests'          => null,
            'note'                => null,
            'cancellation_reason' => null,
        ];
    }

    /**
     * State: crea disponibilità come HOST.
     *
     * Imposta can_host=true, status=AVAILABLE_TO_HOST e genera
     * max_guests (4-10) e un dinner_name casuale.
     */
    public function asHost(): static
    {
        return $this->state(fn () => [
            'can_host'    => true,
            'status'      => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
            'max_guests'  => $this->faker->numberBetween(4, 10),
            'dinner_name' => $this->faker->randomElement([
                'Pizza Napoletana',
                'Pasta al Forno',
                'Sushi Night',
                'BBQ Serata',
                null,
            ]),
            'note' => 'Disponibile ad ospitare!',
        ]);
    }

    /**
     * State: crea disponibilità come GUEST.
     *
     * Imposta can_host=false, status=AVAILABLE, senza max_guests né dinner_name.
     */
    public function asGuest(): static
    {
        return $this->state(fn () => [
            'can_host'    => false,
            'status'      => DinnerAvailabilityStatus::AVAILABLE,
            'max_guests'  => null,
            'dinner_name' => null,
        ]);
    }

    /**
     * State: host con stato ALMOST_FULL.
     *
     * Indica che ci sono già alcune prenotazioni ma non è ancora pieno.
     * Nota: in produzione questo stato è impostato dall'Observer.
     */
    public function almostFull(): static
    {
        return $this->asHost()->state(fn () => [
            'status' => DinnerAvailabilityStatus::ALMOST_FULL,
        ]);
    }

    /**
     * State: host con stato FULL.
     *
     * Indica che tutti i posti sono stati prenotati.
     * Nota: in produzione questo stato è impostato dall'Observer.
     */
    public function full(): static
    {
        return $this->asHost()->state(fn () => [
            'status' => DinnerAvailabilityStatus::FULL,
        ]);
    }

    /**
     * State: host cancellato.
     *
     * Disponibilità cancellata dall'host con motivo PERSONAL_EMERGENCY.
     */
    public function cancelled(): static
    {
        return $this->asHost()->state(fn () => [
            'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
            'cancellation_reason' => CancellationReason::PERSONAL_EMERGENCY,
        ]);
    }

    /**
     * State: completato (cena avvenuta).
     *
     * Stato impostato dal cron job per date passate.
     */
    public function completed(): static
    {
        return $this->asHost()->state(fn () => [
            'status' => DinnerAvailabilityStatus::COMPLETED,
        ]);
    }

    /**
     * State: guest non disponibile.
     *
     * Guest che dichiara di NON essere disponibile per quella data.
     */
    public function notAvailable(): static
    {
        return $this->asGuest()->state(fn () => [
            'status' => DinnerAvailabilityStatus::NOT_AVAILABLE,
        ]);
    }

    /**
     * State: associa a una data cena specifica.
     *
     * @param  \App\Models\DinnerDate  $date  Data cena
     */
    public function forDate(DinnerDate $date): static
    {
        return $this->state(fn () => ['dinner_date_id' => $date->id]);
    }

    /**
     * State: associa a un utente specifico.
     *
     * @param  \App\Models\User  $user  Utente
     */
    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }
}
