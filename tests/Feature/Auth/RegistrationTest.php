<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register as a gestionnaire', function () {
    $response = $this->post('/register', [
        'role' => 'gestionnaire',
        'name' => 'Test Gestionnaire',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'telephone' => '0102030405',
        'adresse' => '1 rue de Test',
        'type_entrepot' => 'city',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new users can register as a fournisseur', function () {
    $response = $this->post('/register', [
        'role' => 'fournisseur',
        'nom' => 'Test Fournisseur',
        'email' => 'fournisseur@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'telephone' => '0102030405',
        'adresse' => '1 rue de Test',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
