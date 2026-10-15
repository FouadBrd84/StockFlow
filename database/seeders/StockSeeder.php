<?php

namespace Database\Seeders;

use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $central = Entrepot::where('type', 'central')->first();
        $produits = Produit::all();

        // Stock initial au dépôt central
        $stocks = [
            'Yaourt Vanille' => 200,
            'Fromage Blanc' => 150,
            'YoapLait Fraise' => 180,
            'Crème Fraîche' => 120,
            'Beurre Doux' => 100,
            'Lait Entier' => 250,
            'Haricots Verts' => 90,
            'Maïs Doux' => 110,
        ];

        foreach ($produits as $produit) {
            if (isset($stocks[$produit->nom])) {
                Stock::create([
                    'produit_id' => $produit->id,
                    'entrepot_id' => $central->id,
                    'quantite' => $stocks[$produit->nom],
                ]);
            }
        }

        // Stock initial dans les magasins
        $lyon = Entrepot::where('nom', 'Carrefour City Lyon')->first();
        $bio = Entrepot::where('nom', 'Carrefour Bio Bordeaux')->first();

        // Lyon
        Stock::create(['produit_id' => Produit::where('nom', 'Yaourt Vanille')->first()->id, 'entrepot_id' => $lyon->id, 'quantite' => 45]);
        Stock::create(['produit_id' => Produit::where('nom', 'Crème Fraîche')->first()->id, 'entrepot_id' => $lyon->id, 'quantite' => 28]);
        Stock::create(['produit_id' => Produit::where('nom', 'Lait Entier')->first()->id, 'entrepot_id' => $lyon->id, 'quantite' => 60]);

        // Bio Bordeaux
        Stock::create(['produit_id' => Produit::where('nom', 'Fromage Blanc')->first()->id, 'entrepot_id' => $bio->id, 'quantite' => 20]);
        Stock::create(['produit_id' => Produit::where('nom', 'Beurre Doux')->first()->id, 'entrepot_id' => $bio->id, 'quantite' => 15]);
        Stock::create(['produit_id' => Produit::where('nom', 'Haricots Verts')->first()->id, 'entrepot_id' => $bio->id, 'quantite' => 35]);
    }
}
