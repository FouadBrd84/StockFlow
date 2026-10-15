<?php

use App\Models\Alerte;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Services\AlerteService;

test('une alerte stock bas est créée automatiquement quand le stock passe sous le seuil (RG07)', function () {
    $entrepot = Entrepot::factory()->central()->create();
    $produit = Produit::factory()->create(['seuil_alerte' => 10]);

    $stock = Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $entrepot->id,
        'quantite' => 8,
    ]);

    AlerteService::verifier($stock->id);

    $this->assertDatabaseHas('alertes', [
        'stock_id' => $stock->id,
        'type' => 'stock_bas',
        'statut' => 'non_lue',
    ]);
});

test('une alerte de rupture est créée quand le stock atteint 0 (RG07)', function () {
    $entrepot = Entrepot::factory()->central()->create();
    $produit = Produit::factory()->create(['seuil_alerte' => 10]);

    $stock = Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $entrepot->id,
        'quantite' => 0,
    ]);

    AlerteService::verifier($stock->id);

    $this->assertDatabaseHas('alertes', [
        'stock_id' => $stock->id,
        'type' => 'rupture',
    ]);
});

test('aucune alerte en double n\'est créée pour le même stock tant qu\'une alerte reste active (RG08)', function () {
    $entrepot = Entrepot::factory()->central()->create();
    $produit = Produit::factory()->create(['seuil_alerte' => 10]);

    $stock = Stock::factory()->create([
        'produit_id' => $produit->id,
        'entrepot_id' => $entrepot->id,
        'quantite' => 5,
    ]);

    AlerteService::verifier($stock->id);
    AlerteService::verifier($stock->id);

    expect(
        Alerte::where('stock_id', $stock->id)->count()
    )->toBe(1);
});
