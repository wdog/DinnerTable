<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
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
            'group_code' => strtoupper(Str::random(14)),
            'created_by' => User::factory(),
        ];
    }
}
