<?php

namespace Database\Seeders;

use App\Models\Entrepot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@stockflow.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Fournisseur 1 — Danone
        User::create([
            'name' => 'Danone',
            'email' => 'danone@stockflow.com',
            'password' => Hash::make('password'),
            'role' => 'fournisseur',
            'telephone' => '0123456789',
            'adresse' => '17 Boulevard Haussmann, 75009 Paris',
        ]);

        // Fournisseur 2 — Reflets de France
        User::create([
            'name' => 'Reflets de France',
            'email' => 'reflets@stockflow.com',
            'password' => Hash::make('password'),
            'role' => 'fournisseur',
            'telephone' => '0987654321',
            'adresse' => '5 Rue de la Paix, 75001 Paris',
        ]);

        // Fournisseur 3 — Bonduelle
        User::create([
            'name' => 'Bonduelle',
            'email' => 'bonduelle@stockflow.com',
            'password' => Hash::make('password'),
            'role' => 'fournisseur',
            'telephone' => '0321654987',
            'adresse' => '1 Rue Nicolas Appert, 59650 Villeneuve-d\'Ascq',
        ]);

        // Gestionnaire 1 — Carrefour City Lyon
        $entrepotLyon = Entrepot::where('nom', 'Carrefour City Lyon')->first();
        User::create([
            'name' => 'Carrefour City Lyon',
            'email' => 'city.lyon@carrefour.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'entrepot_id' => $entrepotLyon->id,
        ]);

        // Gestionnaire 2 — Carrefour Bio Bordeaux
        $entrepotBio = Entrepot::where('nom', 'Carrefour Bio Bordeaux')->first();
        User::create([
            'name' => 'Carrefour Bio Bordeaux',
            'email' => 'bio.bordeaux@carrefour.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'entrepot_id' => $entrepotBio->id,
        ]);

        // Gestionnaire 3 — Carrefour Express Paris
        $entrepotParis = Entrepot::where('nom', 'Carrefour Express Paris')->first();
        User::create([
            'name' => 'Carrefour Express Paris',
            'email' => 'express.paris@carrefour.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'entrepot_id' => $entrepotParis->id,
        ]);
    }
}
