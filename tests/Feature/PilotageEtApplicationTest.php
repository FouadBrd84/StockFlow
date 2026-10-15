<?php

use App\Models\Alerte;
use App\Models\Entrepot;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

test('le dashboard admin affiche la valeur du stock et les prévisions de rupture', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $magasin = Entrepot::factory()->create(['type' => 'city', 'nom' => 'City Lyon']);
    $produit = Produit::factory()->create(['nom' => 'Yaourt vanille', 'prix' => 2, 'seuil_alerte' => 10]);
    $stock = Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $magasin->id, 'quantite' => 6]);
    Mouvement::create(['stock_id' => $stock->id, 'user_id' => $admin->id, 'quantite' => 60, 'motif' => 'vente']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Valeur du stock (réseau)')
        ->assertSee('12 €')
        ->assertSee('Prévisions de rupture')
        ->assertSee('Yaourt vanille')
        ->assertSee('City Lyon')
        ->assertSee('dans 3 j')
        ->assertSee('+32');
});

test('le gestionnaire ne voit que les prévisions de son magasin', function () {
    $monMagasin = Entrepot::factory()->create(['type' => 'city']);
    $autreMagasin = Entrepot::factory()->create(['type' => 'bio']);
    $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'entrepot_id' => $monMagasin->id]);
    $chezMoi = Stock::factory()->create(['entrepot_id' => $monMagasin->id, 'quantite' => 5, 'produit_id' => Produit::factory()->create(['nom' => 'Lait entier'])->id]);
    $ailleurs = Stock::factory()->create(['entrepot_id' => $autreMagasin->id, 'quantite' => 5, 'produit_id' => Produit::factory()->create(['nom' => 'Beurre doux'])->id]);
    Mouvement::create(['stock_id' => $chezMoi->id, 'user_id' => $gestionnaire->id, 'quantite' => 30, 'motif' => 'vente']);
    Mouvement::create(['stock_id' => $ailleurs->id, 'user_id' => $gestionnaire->id, 'quantite' => 30, 'motif' => 'vente']);

    $this->actingAs($gestionnaire)
        ->get(route('gestionnaire.dashboard'))
        ->assertOk()
        ->assertSee('Valeur de mon stock')
        ->assertSee('Lait entier')
        ->assertDontSee('Beurre doux');
});

test('sans sortie récente, les prévisions affichent « Rien à signaler »', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Rien à signaler');
});

test('la cloche du menu affiche le nombre d\'alertes non lues', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $stock = Stock::factory()->create();
    Alerte::create(['stock_id' => $stock->id, 'type' => 'stock_bas', 'message' => 'Stock bas', 'statut' => 'non_lue']);
    Alerte::create(['stock_id' => $stock->id, 'type' => 'rupture', 'message' => 'Rupture', 'statut' => 'non_lue']);
    Alerte::create(['stock_id' => $stock->id, 'type' => 'rupture', 'message' => 'Déjà traitée', 'statut' => 'traitee']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('<span class="sf-count" title="Alertes non lues">2</span>', false)
        ->assertSee('sf-has-alerts', false);
});

test('le compteur du gestionnaire ne compte que les alertes de son magasin', function () {
    $monMagasin = Entrepot::factory()->create(['type' => 'city']);
    $gestionnaire = User::factory()->create(['role' => 'gestionnaire', 'entrepot_id' => $monMagasin->id]);
    Alerte::create(['stock_id' => Stock::factory()->create(['entrepot_id' => $monMagasin->id])->id, 'type' => 'stock_bas', 'message' => 'Chez moi', 'statut' => 'non_lue']);
    Alerte::create(['stock_id' => Stock::factory()->create()->id, 'type' => 'stock_bas', 'message' => 'Ailleurs', 'statut' => 'non_lue']);

    $this->actingAs($gestionnaire)
        ->get(route('gestionnaire.dashboard'))
        ->assertOk()
        ->assertSee('<span class="sf-count" title="Alertes non lues">1</span>', false);
});

test('pas de compteur quand il n\'y a aucune alerte non lue', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee('class="sf-count"', false);
});

test('l\'application est installable : manifeste, icônes, service worker et page hors ligne', function () {
    $manifeste = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

    expect($manifeste['short_name'])->toBe('StockFlow')
        ->and($manifeste['display'])->toBe('standalone')
        ->and(collect($manifeste['icons'])->pluck('sizes'))->toContain('192x192', '512x512');

    foreach (collect($manifeste['icons'])->pluck('src') as $icone) {
        expect(public_path($icone))->toBeFile();
    }

    expect(public_path('sw.js'))->toBeFile()
        ->and(public_path('offline.html'))->toBeFile();

    $this->get(route('home'))->assertOk()->assertSee('manifest.webmanifest', false);
    $this->get(route('login'))->assertOk()->assertSee('manifest.webmanifest', false);
});
