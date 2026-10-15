<?php

use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

test('valider une commande magasin diminue le stock du dépôt central (RG05)', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $admin = User::factory()->create(['role' => 'admin']);
    $produit = Produit::factory()->create();

    Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $central->id,
        'quantite' => 50,
    ]);

    $commande = Commande::factory()->create([
        'entrepot_id' => $magasin->id,
        'statut' => 'en_attente',
    ]);

    CommandeProduit::create([
        'commande_id' => $commande->id,
        'produit_id' => $produit->id,
        'quantite' => 20,
        'prix_unitaire' => $produit->prix,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.commandes-magasin.valider', $commande))
        ->assertRedirect();

    $stockDepot = Stock::where('produit_id', $produit->id)
        ->where('entrepot_id', $central->id)
        ->first();

    expect($stockDepot->quantite)->toBe(30);
    expect($commande->fresh()->statut)->toBe('confirmee');
});

test('un transfert est créé automatiquement lors de la validation', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $admin = User::factory()->create(['role' => 'admin']);
    $produit = Produit::factory()->create();

    Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $central->id,
        'quantite' => 50,
    ]);

    $commande = Commande::factory()->create([
        'entrepot_id' => $magasin->id,
        'statut' => 'en_attente',
    ]);

    CommandeProduit::create([
        'commande_id' => $commande->id,
        'produit_id' => $produit->id,
        'quantite' => 20,
        'prix_unitaire' => $produit->prix,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.commandes-magasin.valider', $commande));

    $stockDestination = Stock::where('produit_id', $produit->id)
        ->where('entrepot_id', $magasin->id)
        ->first();

    $this->assertDatabaseHas('transferts', [
        'stock_destination_id' => $stockDestination->id,
        'statut' => 'en_cours',
    ]);
});
