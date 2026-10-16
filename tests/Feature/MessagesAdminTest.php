<?php

use App\Models\Entrepot;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageAdminNotification;
use Illuminate\Support\Facades\Notification;

function gestionnaireAvecMagasin(): User
{
    return User::factory()->create([
        'role' => 'gestionnaire',
        'entrepot_id' => Entrepot::factory()->create(['type' => 'city'])->id,
    ]);
}

test('un gestionnaire envoie un message : il est enregistré et l\'admin reçoit un email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $gestionnaire = gestionnaireAvecMagasin();
    Notification::fake();

    $this->actingAs($gestionnaire)
        ->post(route('contact.store'), ['sujet' => 'Retard de livraison', 'contenu' => 'La commande #24 n\'est pas arrivée.'])
        ->assertRedirect(route('contact.create'))
        ->assertSessionHas('success');

    $message = Message::sole();
    expect($message->expediteur_id)->toBe($gestionnaire->id)
        ->and($message->sujet)->toBe('Retard de livraison')
        ->and($message->estLu())->toBeFalse();

    Notification::assertSentTo($admin, MessageAdminNotification::class, fn ($notification) => $notification->message->is($message));
});

test('un fournisseur peut aussi contacter l\'admin', function () {
    User::factory()->create(['role' => 'admin']);
    $fournisseur = User::factory()->create(['role' => 'fournisseur']);
    Notification::fake();

    $this->actingAs($fournisseur)->get(route('contact.create'))->assertOk()->assertSee('Contacter l');
    $this->actingAs($fournisseur)
        ->post(route('contact.store'), ['sujet' => 'Nouveau tarif', 'contenu' => 'Nos prix changent le mois prochain.'])
        ->assertSessionHas('success');

    expect(Message::count())->toBe(1);
});

test('l\'admin ne peut pas utiliser le formulaire de contact', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('contact.create'))->assertForbidden();
    $this->actingAs($admin)
        ->post(route('contact.store'), ['sujet' => 'Test', 'contenu' => 'Un message de test.'])
        ->assertForbidden();
});

test('le sujet et un message d\'au moins 10 caractères sont obligatoires', function () {
    $gestionnaire = gestionnaireAvecMagasin();

    $this->actingAs($gestionnaire)
        ->post(route('contact.store'), ['sujet' => '', 'contenu' => 'Court'])
        ->assertSessionHasErrors(['sujet', 'contenu']);

    expect(Message::count())->toBe(0);
});

test('le message est enregistré même si le serveur email est injoignable', function () {
    config([
        'mail.default' => 'smtp',
        'mail.mailers.smtp.host' => 'serveur-inexistant.invalid',
        'mail.mailers.smtp.port' => 587,
    ]);
    User::factory()->create(['role' => 'admin']);

    $this->actingAs(gestionnaireAvecMagasin())
        ->post(route('contact.store'), ['sujet' => 'Urgent', 'contenu' => 'Plus de lait au rayon frais.'])
        ->assertSessionHas('success');

    expect(Message::count())->toBe(1);
});

test('au-delà de 5 messages par heure, l\'envoi est bloqué (anti-spam)', function () {
    User::factory()->create(['role' => 'admin']);
    $gestionnaire = gestionnaireAvecMagasin();
    Notification::fake();

    foreach (range(1, 5) as $numero) {
        $this->actingAs($gestionnaire)
            ->post(route('contact.store'), ['sujet' => 'Message '.$numero, 'contenu' => 'Un message tout à fait normal.'])
            ->assertSessionHas('success');
    }

    $this->actingAs($gestionnaire)
        ->post(route('contact.store'), ['sujet' => 'Message 6', 'contenu' => 'Un message de trop dans l\'heure.'])
        ->assertTooManyRequests();

    expect(Message::count())->toBe(5);
});

test('l\'admin voit ses messages et le compteur de non lus dans le menu', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Message::factory()->create(['sujet' => 'Commande incomplète']);
    Message::factory()->create(['sujet' => 'Question facture']);
    Message::factory()->lu()->create(['sujet' => 'Déjà lu']);

    $this->actingAs($admin)
        ->get(route('admin.messages.index'))
        ->assertOk()
        ->assertSee('Commande incomplète')
        ->assertSee('Question facture')
        ->assertSee('Déjà lu')
        ->assertSee('<span class="sf-count" title="Messages non lus">2</span>', false);
});

test('l\'admin marque un message comme lu', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $message = Message::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.messages.lu', $message))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($message->fresh()->estLu())->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertDontSee('title="Messages non lus"', false);
});

test('un gestionnaire n\'a pas accès à la boîte de réception de l\'admin', function () {
    $this->actingAs(gestionnaireAvecMagasin())
        ->get(route('admin.messages.index'))
        ->assertForbidden();
});

test('l\'email envoyé à l\'admin permet de répondre directement à l\'expéditeur', function () {
    $gestionnaire = gestionnaireAvecMagasin();
    $message = Message::factory()->create(['expediteur_id' => $gestionnaire->id, 'sujet' => 'Rupture de lait']);

    $email = (new MessageAdminNotification($message))->toMail(User::factory()->create(['role' => 'admin']));

    expect($email->subject)->toContain('Rupture de lait')
        ->and($email->replyTo[0][0])->toBe($gestionnaire->email);
});
