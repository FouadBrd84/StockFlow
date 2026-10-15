<?php

use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

test('la réception d\'une commande fonctionne même si le serveur email est injoignable', function () {
    config([
        'mail.default' => 'smtp',
        'mail.mailers.smtp.host' => 'serveur-inexistant.invalid',
        'mail.mailers.smtp.port' => 587,
    ]);

    $central = Entrepot::factory()->central()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $fournisseur = User::factory()->create(['role' => 'fournisseur']);
    $produit = Produit::factory()->create(['fournisseur_id' => $fournisseur->id, 'seuil_alerte' => 10]);
    Stock::factory()->create(['produit_id' => $produit->id, 'entrepot_id' => $central->id, 'quantite' => 100]);

    $commande = Commande::factory()->create([
        'fournisseur_id' => $fournisseur->id,
        'entrepot_id' => $central->id,
        'statut' => 'expediee',
    ]);
    CommandeProduit::create([
        'commande_id' => $commande->id,
        'produit_id' => $produit->id,
        'quantite' => 500,
        'prix_unitaire' => $produit->prix,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.commandes.recevoir', $commande))
        ->assertRedirect(route('admin.commandes.index'))
        ->assertSessionHas('success');

    expect($commande->fresh()->statut)->toBe('recue');
    expect(Stock::where('produit_id', $produit->id)->where('entrepot_id', $central->id)->value('quantite'))->toBe(600);
});
