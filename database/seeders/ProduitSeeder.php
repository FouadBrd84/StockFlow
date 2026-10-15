<?php

namespace Database\Seeders;

use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        $danone = User::where('name', 'Danone')->where('role', 'fournisseur')->first();
        $reflets = User::where('name', 'Reflets de France')->where('role', 'fournisseur')->first();
        $bonduelle = User::where('name', 'Bonduelle')->where('role', 'fournisseur')->first();

        // Produits Danone
        Produit::create([
            'nom' => 'Yaourt Vanille',
            'description' => 'Yaourt nature aromatisé vanille 125g',
            'prix' => 0.85,
            'unite' => 'unité',
            'seuil_alerte' => 50,
            'fournisseur_id' => $danone->id,
        ]);
        Produit::create([
            'nom' => 'Fromage Blanc',
            'description' => 'Fromage blanc 0% 500g',
            'prix' => 1.20,
            'unite' => 'unité',
            'seuil_alerte' => 30,
            'fournisseur_id' => $danone->id,
        ]);
        Produit::create([
            'nom' => 'YoapLait Fraise',
            'description' => 'Yaourt à boire fraise 250ml',
            'prix' => 1.50,
            'unite' => 'unité',
            'seuil_alerte' => 40,
            'fournisseur_id' => $danone->id,
        ]);

        // Produits Reflets de France
        Produit::create([
            'nom' => 'Crème Fraîche',
            'description' => 'Crème fraîche épaisse 30% 20cl',
            'prix' => 1.10,
            'unite' => 'unité',
            'seuil_alerte' => 40,
            'fournisseur_id' => $reflets->id,
        ]);
        Produit::create([
            'nom' => 'Beurre Doux',
            'description' => 'Beurre doux 250g',
            'prix' => 2.50,
            'unite' => 'unité',
            'seuil_alerte' => 30,
            'fournisseur_id' => $reflets->id,
        ]);
        Produit::create([
            'nom' => 'Lait Entier',
            'description' => 'Lait entier 1L',
            'prix' => 0.95,
            'unite' => 'litre',
            'seuil_alerte' => 60,
            'fournisseur_id' => $reflets->id,
        ]);

        // Produits Bonduelle
        Produit::create([
            'nom' => 'Haricots Verts',
            'description' => 'Haricots verts extra fins 400g',
            'prix' => 1.30,
            'unite' => 'unité',
            'seuil_alerte' => 25,
            'fournisseur_id' => $bonduelle->id,
        ]);
        Produit::create([
            'nom' => 'Maïs Doux',
            'description' => 'Maïs doux en boîte 285g',
            'prix' => 0.95,
            'unite' => 'unité',
            'seuil_alerte' => 30,
            'fournisseur_id' => $bonduelle->id,
        ]);
    }
}
