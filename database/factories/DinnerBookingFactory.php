<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per il modello DinnerBooking.
 *
 * Genera prenotazioni per le cene, con vari stati possibili
 * (pending, confirmed, cancelled) e configurazioni personalizzabili.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DinnerBooking>
 */
class DinnerBookingFactory extends Factory
{
    /**
     * Definisce lo stato di default del model.
     *
     * Crea una prenotazione PENDING con 1-3 ospiti e dati casuali.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'host_availability_id' => DinnerAvailability::factory()->asHost(),
            'guest_user_id'        => User::factory(),
            'guests_count'         => $this->faker->numberBetween(1, 3),
            'bringing_items'       => $this->faker->randomElement([
                ['Vino', 'Dolce'],
                ['Antipasto'],
                ['Frutta', 'Pane'],
                [],
            ]),
            'notes'  => $this->faker->randomElement([
                'Non vedo l\'ora!',
                'Grazie per l\'ospitalità',
                null,
            ]),
            'status' => DinnerBookingStatus::PENDING,
        ];
    }

    /**
     * State: prenotazione confermata.
     *
     * Imposta status=CONFIRMED. Questo stato triggera l'aggiornamento
     * automatico dello status host via Observer.
     *
     * @return static
     */
    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => DinnerBookingStatus::CONFIRMED,
        ]);
    }

    /**
     * State: prenotazione in attesa.
     *
     * Imposta status=PENDING (stato di default).
     *
     * @return static
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => DinnerBookingStatus::PENDING,
        ]);
    }

    /**
     * State: prenotazione cancellata.
     *
     * Imposta status=CANCELLED. La cancellazione libera posti
     * e l'Observer aggiorna lo status host.
     *
     * @return static
     */
    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => DinnerBookingStatus::CANCELLED,
        ]);
    }

    /**
     * State: associa a una disponibilità host specifica.
     *
     * @param  \App\Models\DinnerAvailability  $availability  Disponibilità host
     * @return static
     */
    public function forHost(DinnerAvailability $availability): static
    {
        return $this->state(fn () => [
            'host_availability_id' => $availability->id,
        ]);
    }

    /**
     * State: associa a un guest specifico.
     *
     * @param  \App\Models\User  $guest  Utente guest
     * @return static
     */
    public function byGuest(User $guest): static
    {
        return $this->state(fn () => [
            'guest_user_id' => $guest->id,
        ]);
    }

    /**
     * State: imposta numero ospiti specifico.
     *
     * @param  int  $count  Numero di ospiti
     * @return static
     */
    public function withGuests(int $count): static
    {
        return $this->state(fn () => [
            'guests_count' => $count,
        ]);
    }
}
