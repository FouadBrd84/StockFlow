<?php

namespace Database\Factories;

use App\Models\Commande;
use App\Models\Entrepot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commande>
 */
class CommandeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fournisseur_id' => null,
            'entrepot_id' => Entrepot::factory(),
            'statut' => 'en_attente',
            'note' => null,
        ];
    }
}
