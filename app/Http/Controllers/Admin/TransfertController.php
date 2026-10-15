<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Entrepot;
use App\Models\Stock;
use App\Models\Transfert;
use App\Services\AlerteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Valide une commande magasin en « tout ou rien » (RG06 puis RG05).
     *
     * La commande et les stocks du dépôt sont verrouillés dans une transaction :
     * deux validations simultanées ne peuvent ni valider deux fois la même commande,
     * ni faire passer le stock du dépôt sous zéro.
     */
    public function validerCommande(Commande $commande): RedirectResponse
    {
        $stocksDepotModifies = [];

        $erreur = DB::transaction(function () use ($commande, &$stocksDepotModifies): ?string {
            $commande = Commande::lockForUpdate()->findOrFail($commande->id);

            if ($commande->statut !== 'en_attente') {
                return 'Cette commande ne peut pas être validée.';
            }

            $central = Entrepot::where('type', 'central')->firstOrFail();
            $magasin = $commande->entrepot;
            $produits = $commande->produits;

            $stocksDepot = Stock::where('entrepot_id', $central->id)
                ->whereIn('produit_id', $produits->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('produit_id');

            foreach ($produits as $produit) {
                $stock = $stocksDepot->get($produit->id);

                if (! $stock || $stock->quantite < $produit->pivot->quantite) {
                    return "Stock insuffisant pour : {$produit->nom}";
                }
            }

            foreach ($produits as $produit) {
                $stockSource = $stocksDepot->get($produit->id);

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

                $stocksDepotModifies[] = $stockSource->id;
            }

            $commande->update(['statut' => 'confirmee']);

            return null;
        });

        if ($erreur !== null) {
            return back()->with('error', $erreur);
        }

        foreach (array_unique($stocksDepotModifies) as $stockId) {
            AlerteService::verifier($stockId);
        }

        return redirect()->route('admin.magasins.index')
            ->with('success', 'Commande validée — transfert créé.');
    }

    public function commandesMagasin()
    {
        return $this->index();
    }
}
