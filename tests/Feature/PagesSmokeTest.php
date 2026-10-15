<?php

use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

test('toutes les pages principales admin s\'affichent sans erreur', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $fournisseur = User::factory()->create(['role' => 'fournisseur']);
    $produit = Produit::factory()->create(['fournisseur_id' => $fournisseur->id]);
    Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $central->id]);

    $commande = Commande::factory()->create(['fournisseur_id' => $fournisseur->id, 'entrepot_id' => $central->id]);
    CommandeProduit::create(['commande_id' => $commande->id, 'produit_id' => $produit->id, 'quantite' => 5, 'prix_unitaire' => $produit->prix]);

    $commandeMagasin = Commande::factory()->create(['fournisseur_id' => null, 'entrepot_id' => $magasin->id]);
    CommandeProduit::create(['commande_id' => $commandeMagasin->id, 'produit_id' => $produit->id, 'quantite' => 3, 'prix_unitaire' => $produit->prix]);

    $admin = User::factory()->create(['role' => 'admin']);

    $routes = [
        'admin.dashboard',
        'admin.produits.index',
        'admin.fournisseurs.index',
        'admin.commandes.index',
        'admin.magasins.index',
        'admin.mouvements.index',
        'admin.stocks.index',
        'admin.users.index',
        'admin.alertes.index',
    ];

    foreach ($routes as $route) {
        $this->actingAs($admin)->get(route($route))->assertOk();
    }
});

test('toutes les pages principales gestionnaire s\'affichent sans erreur', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $central = Entrepot::factory()->central()->create();
    $produit = Produit::factory()->create();
    Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $central->id]);
    Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id]);

    $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'entrepot_id' => $magasin->id]);

    $commande = Commande::factory()->create(['fournisseur_id' => null, 'entrepot_id' => $magasin->id]);
    CommandeProduit::create(['commande_id' => $commande->id, 'produit_id' => $produit->id, 'quantite' => 2, 'prix_unitaire' => $produit->prix]);

    $routes = [
        'gestionnaire.dashboard',
        'gestionnaire.stocks.index',
        'gestionnaire.commandes.index',
        'gestionnaire.commandes.create',
        'gestionnaire.sorties.index',
        'gestionnaire.mouvements.index',
        'gestionnaire.alertes.index',
    ];

    foreach ($routes as $route) {
        $this->actingAs($gestionnaire)->get(route($route))->assertOk();
    }

    $this->actingAs($gestionnaire)->get(route('gestionnaire.commandes.show', $commande))->assertOk();
});

test('toutes les pages principales fournisseur s\'affichent sans erreur', function () {
    $fournisseur = User::factory()->create(['role' => 'fournisseur']);
    Produit::factory()->create(['fournisseur_id' => $fournisseur->id]);

    $routes = [
        'fournisseur.dashboard',
        'fournisseur.produits.index',
        'fournisseur.commandes.index',
    ];

    foreach ($routes as $route) {
        $this->actingAs($fournisseur)->get(route($route))->assertOk();
    }
});
