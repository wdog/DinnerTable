<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DinnerGroup>
 */
class DinnerGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->words(3, true) . ' Dinner Group',
            'slogan'     => fake()->sentence(),
            'group_code' => strtoupper(\Illuminate\Support\Str::random(14)),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
