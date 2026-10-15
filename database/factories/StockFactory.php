<?php

namespace Database\Factories;

use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'produit_id' => Produit::factory(),
            'entrepot_id' => Entrepot::factory(),
            'quantite' => fake()->numberBetween(20, 100),
        ];
    }
}
