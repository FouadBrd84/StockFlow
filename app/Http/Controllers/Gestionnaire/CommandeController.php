<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Transfert;
use App\Notifications\CommandeRecueNotification;
use App\Services\AlerteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function index()
    {
        $entrepot = Auth::user()->entrepot;
        $commandes = Commande::with(['produits', 'entrepot'])
            ->where('entrepot_id', $entrepot->id)
            ->latest()->get();

        return view('gestionnaire.commandes.index', compact('commandes'));
    }

    public function create()
    {
        $produits = Stock::with('produit')
            ->whereHas('entrepot', fn ($q) => $q->where('type', 'central'))
            ->where('quantite', '>', 0)
            ->get();

        return view('gestionnaire.commandes.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produits' => 'required|array|min:1',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
        ]);

        $entrepot = Auth::user()->entrepot;
        $central = Entrepot::where('type', 'central')->first();

        // Regrouper les quantités par produit pour éviter les doublons
        $produitsGroupes = [];
        foreach ($request->produits as $item) {
            if (! empty($item['id']) && ! empty($item['quantite'])) {
                $id = $item['id'];
                if (isset($produitsGroupes[$id])) {
                    $produitsGroupes[$id] += $item['quantite'];
                } else {
                    $produitsGroupes[$id] = $item['quantite'];
                }
            }
        }

        // Vérifier le stock disponible
        foreach ($produitsGroupes as $produitId => $quantiteTotale) {
            $stock = Stock::where('produit_id', $produitId)
                ->where('entrepot_id', $central->id)
                ->first();
            $produit = Produit::find($produitId);

            if (! $stock || $stock->quantite < $quantiteTotale) {
                return back()
                    ->withInput()
                    ->with('error', "Stock insuffisant pour : {$produit->nom}. Disponible : ".($stock->quantite ?? 0)." / Demandé : {$quantiteTotale}");
            }
        }

        $commande = Commande::create([
            'fournisseur_id' => null,
            'entrepot_id' => $entrepot->id,
            'statut' => 'en_attente',
            'note' => $request->note,
        ]);

        foreach ($produitsGroupes as $produitId => $quantiteTotale) {
            $produit = Produit::find($produitId);
            CommandeProduit::create([
                'commande_id' => $commande->id,
                'produit_id' => $produitId,
                'quantite' => $quantiteTotale,
                'prix_unitaire' => $produit->prix,
            ]);
        }

        return redirect()->route('gestionnaire.commandes.index')
            ->with('success', 'Commande passée avec succès.');
    }

    public function show(Commande $commande)
    {
        $commande->load(['produits', 'entrepot']);

        return view('gestionnaire.commandes.show', compact('commande'));
    }

    public function recevoir(Commande $commande)
    {
        if ($commande->statut !== 'confirmee') {
            return back()->with('error', 'Cette commande ne peut pas être reçue.');
        }

        $entrepot = Auth::user()->entrepot;

        foreach ($commande->produits as $produit) {
            $stock = Stock::where('produit_id', $produit->id)
                ->where('entrepot_id', $entrepot->id)
                ->first();

            if ($stock) {
                $stock->increment('quantite', $produit->pivot->quantite);
            } else {
                $stock = Stock::create([
                    'produit_id' => $produit->id,
                    'entrepot_id' => $entrepot->id,
                    'quantite' => $produit->pivot->quantite,
                ]);
            }

            AlerteService::verifier($stock->id);
        }

        $commande->update(['statut' => 'recue']);

        Transfert::where('commande_id', $commande->id)
            ->where('statut', 'en_cours')
            ->update(['statut' => 'termine']);

        // Notification email
        Auth::user()->notifierSansBloquer(new CommandeRecueNotification($commande));

        return redirect()->route('gestionnaire.commandes.index')
            ->with('success', 'Réception confirmée — stock mis à jour !');
    }
}
