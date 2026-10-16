<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Une vraie image PNG de 192 × 192 pixels (l'icône de l'application).
 */
function photoValide(string $nom = 'photo.png'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($nom, file_get_contents(public_path('icons/icon-192.png')));
}

beforeEach(function () {
    Storage::fake('local');
});

test('un utilisateur ajoute une photo de profil, stockée dans un dossier privé', function () {
    $utilisateur = User::factory()->create(['role' => 'gestionnaire']);

    $this->actingAs($utilisateur)
        ->post(route('profil.photo.update'), ['photo' => photoValide()])
        ->assertRedirect(route('profil.edit'))
        ->assertSessionHas('success');

    $photo = $utilisateur->fresh()->photo;
    expect($photo)->toStartWith('avatars/');
    Storage::disk('local')->assertExists($photo);
});

test('changer de photo supprime l\'ancienne', function () {
    $utilisateur = User::factory()->create();

    $this->actingAs($utilisateur)->post(route('profil.photo.update'), ['photo' => photoValide('premiere.png')]);
    $ancienne = $utilisateur->fresh()->photo;

    $this->actingAs($utilisateur)->post(route('profil.photo.update'), ['photo' => photoValide('seconde.png')]);
    $nouvelle = $utilisateur->fresh()->photo;

    expect($nouvelle)->not->toBe($ancienne);
    Storage::disk('local')->assertMissing($ancienne);
    Storage::disk('local')->assertExists($nouvelle);
});

test('refuse un fichier qui n\'est pas une image', function () {
    $utilisateur = User::factory()->create();

    $this->actingAs($utilisateur)
        ->post(route('profil.photo.update'), ['photo' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')])
        ->assertSessionHasErrors('photo');

    expect($utilisateur->fresh()->photo)->toBeNull();
});

test('refuse une image trop petite', function () {
    $utilisateur = User::factory()->create();
    $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

    $this->actingAs($utilisateur)
        ->post(route('profil.photo.update'), ['photo' => UploadedFile::fake()->createWithContent('pixel.png', $pixel)])
        ->assertSessionHasErrors('photo');
});

test('un utilisateur supprime sa photo', function () {
    $utilisateur = User::factory()->create();
    $this->actingAs($utilisateur)->post(route('profil.photo.update'), ['photo' => photoValide()]);
    $photo = $utilisateur->fresh()->photo;

    $this->actingAs($utilisateur)
        ->delete(route('profil.photo.destroy'))
        ->assertRedirect(route('profil.edit'));

    expect($utilisateur->fresh()->photo)->toBeNull();
    Storage::disk('local')->assertMissing($photo);
});

test('la photo est visible par les utilisateurs connectés, pas par les visiteurs', function () {
    $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
    $this->actingAs($gestionnaire)->post(route('profil.photo.update'), ['photo' => photoValide()]);
    $gestionnaire->refresh();

    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('photos.show', $gestionnaire))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    auth()->logout();
    $this->get(route('photos.show', $gestionnaire))->assertRedirect(route('login'));
});

test('renvoie 404 si l\'utilisateur n\'a pas de photo', function () {
    $sansPhoto = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('photos.show', $sansPhoto))
        ->assertNotFound();
});

test('la photo remplace l\'initiale dans la barre de navigation', function () {
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin']);
    $this->actingAs($admin)->post(route('profil.photo.update'), ['photo' => photoValide()]);
    $admin->refresh();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('sf-avatar-photo', false)
        ->assertSee($admin->urlPhoto(), false)
        ->assertDontSee('<span class="sf-avatar">A</span>', false);
});
