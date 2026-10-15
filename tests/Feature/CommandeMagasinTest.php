<?php

use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

test('un gestionnaire peut commander dans la limite du stock disponible (RG06)', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $gestionnaire = User::factory()->create([
        'role' => 'gestionnaire',
        'entrepot_id' => $magasin->id,
    ]);
    $produit = Produit::factory()->create();

    Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $central->id,
        'quantite' => 35,
    ]);

    $this->actingAs($gestionnaire)
        ->post(route('gestionnaire.commandes.store'), [
            'produits' => [
                ['id' => $produit->id, 'quantite' => 30],
            ],
        ])
        ->assertRedirect(route('gestionnaire.commandes.index'));

    $this->assertDatabaseHas('commande_produit', [
        'produit_id' => $produit->id,
        'quantite' => 30,
    ]);
});

test('un gestionnaire ne peut pas commander plus que le stock disponible au dépôt (RG06)', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $gestionnaire = User::factory()->create([
        'role' => 'gestionnaire',
        'entrepot_id' => $magasin->id,
    ]);
    $produit = Produit::factory()->create();

    Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $central->id,
        'quantite' => 35,
    ]);

    $this->actingAs($gestionnaire)
        ->post(route('gestionnaire.commandes.store'), [
            'produits' => [
                ['id' => $produit->id, 'quantite' => 40],
            ],
        ])
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('commande_produit', [
        'produit_id' => $produit->id,
        'quantite' => 40,
    ]);
});
