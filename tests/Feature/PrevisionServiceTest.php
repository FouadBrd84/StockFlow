<?php

use App\Models\Entrepot;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Transfert;
use App\Models\User;
use App\Services\PrevisionService;

/**
 * Crée une sortie (vente) sur un stock, éventuellement dans le passé.
 */
function vendre(Stock $stock, int $quantite, int $ilYaJours = 1): Mouvement
{
    $mouvement = Mouvement::create([
        'stock_id' => $stock->id,
        'user_id' => User::factory()->create()->id,
        'quantite' => $quantite,
        'motif' => 'vente',
    ]);
    $mouvement->forceFill(['created_at' => now()->subDays($ilYaJours)])->save();

    return $mouvement;
}

test('calcule la consommation par jour, les jours restants et la quantité à recommander', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $produit = Produit::factory()->create(['seuil_alerte' => 10]);
    $stock = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id, 'quantite' => 6]);

    vendre($stock, 30, 5);
    vendre($stock, 30, 20);

    $prevision = app(PrevisionService::class)->pourEntrepot($magasin->id)->first();

    expect($prevision['consommation_jour'])->toBe(2.0)
        ->and($prevision['jours_restants'])->toBe(3)
        ->and($prevision['niveau'])->toBe('critique')
        ->and($prevision['a_commander'])->toBe(32)
        ->and($prevision['date_rupture']->isSameDay(now()->addDays(3)))->toBeTrue();
});

test('un stock confortable ne demande pas de recommande', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $produit = Produit::factory()->create(['seuil_alerte' => 10]);
    $stock = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id, 'quantite' => 400]);

    vendre($stock, 60);

    $prevision = app(PrevisionService::class)->pourEntrepot($magasin->id)->first();

    expect($prevision['jours_restants'])->toBe(200)
        ->and($prevision['niveau'])->toBe('ok')
        ->and($prevision['a_commander'])->toBe(0);
});

test('ignore les stocks sans sortie récente et les ventes trop anciennes', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $sansVente = Stock::factory()->create(['entrepot_id' => $magasin->id, 'quantite' => 50]);
    $venteAncienne = Stock::factory()->create(['entrepot_id' => $magasin->id, 'quantite' => 50]);

    vendre($venteAncienne, 40, 45);

    expect(app(PrevisionService::class)->pourEntrepot($magasin->id))->toBeEmpty();
});

test('les transferts qui partent du dépôt comptent dans sa consommation', function () {
    $central = Entrepot::factory()->central()->create();
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $produit = Produit::factory()->create();
    $stockDepot = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $central->id, 'quantite' => 90]);
    $stockMagasin = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id, 'quantite' => 0]);

    Transfert::enregistrer([
        'stock_source_id' => $stockDepot->id,
        'stock_destination_id' => $stockMagasin->id,
        'user_id' => User::factory()->create()->id,
        'quantite' => 90,
    ]);

    $prevision = app(PrevisionService::class)->pourEntrepot($central->id)->first();

    expect($prevision['stock_id'])->toBe($stockDepot->id)
        ->and($prevision['consommation_jour'])->toBe(3.0)
        ->and($prevision['jours_restants'])->toBe(30);
});

test('trie du plus urgent au moins urgent et filtre par entrepôt', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $autreMagasin = Entrepot::factory()->create(['type' => 'bio']);
    $tranquille = Stock::factory()->create(['entrepot_id' => $magasin->id, 'quantite' => 300]);
    $urgent = Stock::factory()->create(['entrepot_id' => $magasin->id, 'quantite' => 4]);
    $ailleurs = Stock::factory()->create(['entrepot_id' => $autreMagasin->id, 'quantite' => 1]);

    vendre($tranquille, 30);
    vendre($urgent, 30);
    vendre($ailleurs, 30);

    $previsions = app(PrevisionService::class)->pourEntrepot($magasin->id);

    expect($previsions->pluck('stock_id')->all())->toBe([$urgent->id, $tranquille->id]);
});

test('calcule la valeur du stock en euros', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $autreMagasin = Entrepot::factory()->create(['type' => 'bio']);
    Stock::factory()->create(['entrepot_id' => $magasin->id, 'quantite' => 10, 'produit_id' => Produit::factory()->create(['prix' => 2.5])->id]);
    Stock::factory()->create(['entrepot_id' => $magasin->id, 'quantite' => 5, 'produit_id' => Produit::factory()->create(['prix' => 1])->id]);
    Stock::factory()->create(['entrepot_id' => $autreMagasin->id, 'quantite' => 4, 'produit_id' => Produit::factory()->create(['prix' => 10])->id]);

    $service = app(PrevisionService::class);

    expect($service->valeurStock($magasin->id))->toBe(30.0)
        ->and($service->valeurStock())->toBe(70.0);
});
