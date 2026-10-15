<?php

use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Transfert;
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

test('une commande validée deux fois ne retire le stock qu\'une seule fois', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $admin = User::factory()->create(['role' => 'admin']);
    $produit = Produit::factory()->create();
    $stockDepot = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $central->id, 'quantite' => 50]);
    $commande = Commande::factory()->create(['entrepot_id' => $magasin->id, 'statut' => 'en_attente']);
    CommandeProduit::create(['commande_id' => $commande->id, 'produit_id' => $produit->id, 'quantite' => 20, 'prix_unitaire' => $produit->prix]);

    $this->actingAs($admin)->post(route('admin.commandes-magasin.valider', $commande))->assertSessionHas('success');
    $this->actingAs($admin)->post(route('admin.commandes-magasin.valider', $commande))
        ->assertSessionHas('error', 'Cette commande ne peut pas être validée.');

    expect($stockDepot->fresh()->quantite)->toBe(30)
        ->and(Transfert::where('commande_id', $commande->id)->count())->toBe(1);
});

test('un stock insuffisant ne modifie rien (tout ou rien)', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $admin = User::factory()->create(['role' => 'admin']);
    $assezDeStock = Produit::factory()->create();
    $pasAssez = Produit::factory()->create(['nom' => 'Yaourt vanille']);
    $stockOk = Stock::factory()->create(['produit_id' => $assezDeStock->id, 'entrepot_id' => $central->id, 'quantite' => 50]);
    Stock::factory()->create(['produit_id' => $pasAssez->id, 'entrepot_id' => $central->id, 'quantite' => 35]);
    $commande = Commande::factory()->create(['entrepot_id' => $magasin->id, 'statut' => 'en_attente']);
    CommandeProduit::create(['commande_id' => $commande->id, 'produit_id' => $assezDeStock->id, 'quantite' => 20, 'prix_unitaire' => 1]);
    CommandeProduit::create(['commande_id' => $commande->id, 'produit_id' => $pasAssez->id, 'quantite' => 40, 'prix_unitaire' => 1]);

    $this->actingAs($admin)->post(route('admin.commandes-magasin.valider', $commande))
        ->assertSessionHas('error', 'Stock insuffisant pour : Yaourt vanille');

    expect($stockOk->fresh()->quantite)->toBe(50)
        ->and($commande->fresh()->statut)->toBe('en_attente')
        ->and(Transfert::count())->toBe(0);
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
