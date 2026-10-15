<?php

use App\Models\Entrepot;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Transfert;
use App\Models\User;

test('un transfert reçu du dépôt apparaît comme une entrée dans l\'historique du magasin', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $admin = User::factory()->create(['role' => 'admin']);
    $gestionnaire = User::factory()->create([
        'role' => 'gestionnaire',
        'entrepot_id' => $magasin->id,
    ]);
    $produit = Produit::factory()->create();

    $stockDepot = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $central->id, 'quantite' => 100]);
    $stockMagasin = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id, 'quantite' => 0]);

    Transfert::enregistrer([
        'stock_source_id' => $stockDepot->id,
        'stock_destination_id' => $stockMagasin->id,
        'user_id' => $admin->id,
        'quantite' => 40,
        'note' => 'Transfert vers '.$magasin->nom,
    ]);

    $this->actingAs($gestionnaire)
        ->get(route('gestionnaire.mouvements.index'))
        ->assertOk()
        ->assertSee('⬆ Entrée')
        ->assertDontSee('⬇ Sortie');
});

test('une vente reste une sortie dans l\'historique du magasin', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $gestionnaire = User::factory()->create([
        'role' => 'gestionnaire',
        'entrepot_id' => $magasin->id,
    ]);
    $produit = Produit::factory()->create();
    $stockMagasin = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id, 'quantite' => 50]);

    Mouvement::create([
        'stock_id' => $stockMagasin->id,
        'user_id' => $gestionnaire->id,
        'quantite' => 5,
        'motif' => 'vente',
    ]);

    $this->actingAs($gestionnaire)
        ->get(route('gestionnaire.mouvements.index'))
        ->assertOk()
        ->assertSee('⬇ Sortie')
        ->assertDontSee('⬆ Entrée');
});
