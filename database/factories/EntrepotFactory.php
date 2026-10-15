<?php

namespace Database\Factories;

use App\Models\Entrepot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entrepot>
 */
class EntrepotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->city(),
            'type' => fake()->randomElement(['city', 'express', 'market', 'hypermarket', 'bio', 'drive', 'proximite', 'flash']),
            'adresse' => fake()->address(),
            'telephone' => fake()->phoneNumber(),
        ];
    }

    /**
     * Indicate that the warehouse is the central depot.
     */
    public function central(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => 'Dépôt central',
            'type' => 'central',
        ]);
    }
}
