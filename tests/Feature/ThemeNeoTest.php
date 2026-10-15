<?php

use App\Models\Entrepot;
use App\Models\User;

test('les pages internes chargent le thème Neo (police, styles et animations)', function () {
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('sf-nav', false)
        ->assertSee('family=Outfit', false)
        ->assertSee('css/stockflow.css?v=', false)
        ->assertSee('js/stockflow.js?v=', false);
});

test('la barre de navigation affiche l\'initiale de l\'utilisateur et met en avant les alertes', function () {
    $magasin = Entrepot::factory()->create(['type' => 'city']);
    $gestionnaire = User::factory()->create([
        'role' => 'gestionnaire',
        'name' => 'city lyon',
        'entrepot_id' => $magasin->id,
    ]);

    $this->actingAs($gestionnaire)
        ->get(route('gestionnaire.dashboard'))
        ->assertOk()
        ->assertSee('<span class="sf-avatar">C</span>', false)
        ->assertSee('sf-nav-alert', false);
});

test('les fichiers du thème existent dans public', function () {
    expect(public_path('css/stockflow.css'))->toBeFile()
        ->and(public_path('css/stockflow-auth.css'))->toBeFile()
        ->and(public_path('js/stockflow.js'))->toBeFile();
});

test('les pages d\'authentification chargent le thème Neo', function (string $route) {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('css/stockflow-auth.css?v=', false)
        ->assertSee('family=Outfit', false);
})->with(['login', 'register', 'password.request']);

test('la page de connexion affiche le salut animé', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('class="sf-hello"', false);
});
