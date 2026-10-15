<?php

namespace Database\Seeders;

use App\Models\Entrepot;
use Illuminate\Database\Seeder;

class EntrepotSeeder extends Seeder
{
    public function run(): void
    {
        // Dépôt central
        Entrepot::create([
            'nom' => 'Entrepôt Central IDF',
            'type' => 'central',
            'adresse' => '1 Rue de la Logistique, 94000 Créteil',
            'telephone' => '0123456789',
        ]);

        // Magasins
        Entrepot::create([
            'nom' => 'Carrefour City Lyon',
            'type' => 'city',
            'adresse' => '1 Route de Lyon, 69200 Lyon',
            'telephone' => '0456789123',
        ]);

        Entrepot::create([
            'nom' => 'Carrefour Bio Bordeaux',
            'type' => 'bio',
            'adresse' => '49 Rue du Pas Saint-Georges, 33000 Bordeaux',
            'telephone' => '0567891234',
        ]);

        Entrepot::create([
            'nom' => 'Carrefour Express Paris',
            'type' => 'express',
            'adresse' => '11 Rue d\'Estrées, 75007 Paris',
            'telephone' => '0678912345',
        ]);

        Entrepot::create([
            'nom' => 'Carrefour Market Avignon',
            'type' => 'market',
            'adresse' => '3 Boulevard Saint-Michel, 84000 Avignon',
            'telephone' => '0789123456',
        ]);
    }
}
