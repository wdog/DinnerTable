<?php

namespace Database\Factories;

use App\Models\DinnerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per il modello DinnerDate.
 *
 * Genera date cena per i gruppi, con possibilità di specificare
 * date future, passate o per un gruppo specifico.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DinnerDate>
 */
class DinnerDateFactory extends Factory
{
    /**
     * Definisce lo stato di default del model.
     *
     * Genera una data cena futura (tra oggi e +3 mesi) per un nuovo gruppo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dinner_group_id' => DinnerGroup::factory(),
            'dinner_date'     => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
        ];
    }

    /**
     * State: imposta una data futura specifica.
     *
     * Utile per testare scenari con date precise.
     *
     * @param  string  $date  Data in formato Y-m-d
     */
    public function futureDate(string $date): static
    {
        return $this->state(fn () => ['dinner_date' => $date]);
    }

    /**
     * State: genera una data passata.
     *
     * Utile per testare scope past() e disponibilità completate.
     */
    public function pastDate(): static
    {
        return $this->state(fn () => [
            'dinner_date' => $this->faker->dateTimeBetween('-2 months', 'yesterday')->format('Y-m-d'),
        ]);
    }

    /**
     * State: associa la data cena a un gruppo specifico.
     *
     * Utile per testare scenari multi-gruppo.
     *
     * @param  \App\Models\DinnerGroup  $group  Gruppo di riferimento
     */
    public function forGroup(DinnerGroup $group): static
    {
        return $this->state(fn () => ['dinner_group_id' => $group->id]);
    }
}
