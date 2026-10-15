<?php

use App\Models\Alerte;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

test('la page des alertes admin s\'affiche sans erreur', function () {
    $entrepot = Entrepot::factory()->central()->create();
    $produit = Produit::factory()->create();
    $stock = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $entrepot->id]);
    Alerte::create(['stock_id' => $stock->id, 'type' => 'stock_bas', 'message' => 'Stock bas', 'statut' => 'non_lue']);

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.alertes.index'))
        ->assertOk()
        ->assertSee('Stock bas');
});

test('la page des alertes gestionnaire s\'affiche sans erreur', function () {
    $entrepot = Entrepot::factory()->create(['type' => 'city']);
    $produit = Produit::factory()->create();
    $stock = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $entrepot->id]);
    Alerte::create(['stock_id' => $stock->id, 'type' => 'rupture', 'message' => 'Rupture de stock', 'statut' => 'non_lue']);

    $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'entrepot_id' => $entrepot->id]);

    $this->actingAs($gestionnaire)
        ->get(route('gestionnaire.alertes.index'))
        ->assertOk()
        ->assertSee('Rupture de stock');
});
