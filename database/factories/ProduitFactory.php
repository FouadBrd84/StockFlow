<?php

namespace Database\Factories;

use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produit>
 */
class ProduitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'prix' => fake()->randomFloat(2, 1, 50),
            'unite' => 'unité',
            'seuil_alerte' => 10,
            'fournisseur_id' => User::factory()->state(['role' => 'fournisseur']),
        ];
    }
}
