<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Entrepot;
use App\Models\Stock;
use App\Models\Transfert;
use App\Services\AlerteService;
use Illuminate\Support\Facades\Auth;

class TransfertController extends Controller
{
    public function index()
    {
        // Commandes en attente
        $commandesEnAttente = Commande::with(['entrepot', 'produits'])
            ->whereNull('fournisseur_id')
            ->where('statut', 'en_attente')
            ->latest()->get();

        // Commandes en cours
        $commandesEnCours = Commande::with(['entrepot', 'produits'])
            ->whereNull('fournisseur_id')
            ->where('statut', 'confirmee')
            ->latest()->get();

        // Commandes livrées
        $commandesLivrees = Commande::with(['entrepot', 'produits'])
            ->whereNull('fournisseur_id')
            ->where('statut', 'recue')
            ->latest()->get();

        return view('admin.magasins.index', compact(
            'commandesEnAttente', 'commandesEnCours', 'commandesLivrees'
        ));
    }

    public function validerCommande(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Cette commande ne peut pas être validée.');
        }

        $central = Entrepot::where('type', 'central')->first();
        $magasin = $commande->entrepot;

        foreach ($commande->produits as $produit) {
            $stock = Stock::where('produit_id', $produit->id)
                ->where('entrepot_id', $central->id)
                ->first();

            if (! $stock || $stock->quantite < $produit->pivot->quantite) {
                return back()->with('error', "Stock insuffisant pour : {$produit->nom}");
            }
        }

        foreach ($commande->produits as $produit) {
            $stockSource = Stock::where('produit_id', $produit->id)
                ->where('entrepot_id', $central->id)
                ->first();

            $stockDestination = Stock::firstOrCreate(
                ['produit_id' => $produit->id, 'entrepot_id' => $magasin->id],
                ['quantite' => 0]
            );

            $stockSource->decrement('quantite', $produit->pivot->quantite);

            Transfert::enregistrer([
                'stock_source_id' => $stockSource->id,
                'stock_destination_id' => $stockDestination->id,
                'user_id' => Auth::id(),
                'quantite' => $produit->pivot->quantite,
                'commande_id' => $commande->id,
                'statut' => 'en_cours',
                'note' => 'Transfert vers '.$magasin->nom,
            ]);

            AlerteService::verifier($stockSource->id);
        }

        $commande->update(['statut' => 'confirmee']);

        return redirect()->route('admin.magasins.index')
            ->with('success', 'Commande validée — transfert créé.');
    }

    public function commandesMagasin()
    {
        return $this->index();
    }
}
