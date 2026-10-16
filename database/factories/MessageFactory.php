<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expediteur_id' => User::factory()->state(['role' => 'gestionnaire']),
            'sujet' => fake()->sentence(4),
            'contenu' => fake()->paragraph(),
            'lu_at' => null,
        ];
    }

    /**
     * Message déjà lu par l'administrateur.
     */
    public function lu(): static
    {
        return $this->state(fn (array $attributes) => [
            'lu_at' => now(),
        ]);
    }
}
