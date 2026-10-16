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

test('la page d\'accueil présente la nouvelle identité StockFlow', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('images/stockflow-logo.svg', false)
        ->assertSee('Rien de trop.')
        ->assertSee('id="fonctionnalites"', false)
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false);
});

test('la barre de navigation propose la palette de commandes Ctrl + K', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('data-sf-cmdk', false)
        ->assertSee('images/stockflow-logo.svg', false);
});

test('la barre propose les trois thèmes et les applique avant l\'affichage', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('data-sf-theme="neo"', false)
        ->assertSee('data-sf-theme="prestige"', false)
        ->assertSee('data-sf-theme="champions"', false)
        ->assertSee('css/stockflow-themes.css?v=', false)
        ->assertSee("localStorage.getItem('sf-theme')", false)
        ->assertSee('family=Barlow+Condensed', false);
});

test('les pages de connexion respectent aussi le thème choisi', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('css/stockflow-themes.css?v=', false)
        ->assertSee("localStorage.getItem('sf-theme')", false);
});

test('le mode contraste élevé reste prioritaire sur les thèmes sombres', function () {
    $css = file_get_contents(public_path('css/stockflow-themes.css'));

    expect($css)->toContain('html[data-theme="prestige"] body:not(.contraste-eleve)')
        ->and($css)->toContain('html[data-theme="champions"] body:not(.contraste-eleve)');
});

test('les invités ne voient pas la palette de commandes', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('data-sf-cmdk', false);
});

test('la page de connexion affiche le salut animé', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('class="sf-hello"', false);
});
