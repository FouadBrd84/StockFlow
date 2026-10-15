<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\Entree;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\CommandeRecueNotification;
use App\Services\AlerteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['fournisseur', 'entrepot'])
            ->whereNotNull('fournisseur_id')
            ->latest()->get();

        return view('admin.commandes.index', compact('commandes'));
    }

    public function create()
    {
        $fournisseurs = User::where('role', 'fournisseur')->get();
        $entrepot = Entrepot::where('type', 'central')->first();
        $produits = collect();

        return view('admin.commandes.create', compact('fournisseurs', 'produits', 'entrepot'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => ['required', Rule::exists('users', 'id')->where('role', 'fournisseur')],
            'produits' => 'required|array|min:1',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
        ]);

        $entrepot = Entrepot::where('type', 'central')->first();

        $commande = Commande::create([
            'fournisseur_id' => $request->fournisseur_id,
            'entrepot_id' => $entrepot->id,
            'statut' => 'en_attente',
            'note' => $request->note,
        ]);

        foreach ($request->produits as $item) {
            if (! empty($item['id']) && ! empty($item['quantite'])) {
                $produit = Produit::find($item['id']);
                CommandeProduit::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $item['id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $produit->prix,
                ]);
            }
        }

        return redirect()->route('admin.commandes.index')
            ->with('success', 'Commande créée avec succès.');
    }

    public function show(Commande $commande)
    {
        $commande->load(['fournisseur', 'entrepot', 'produits']);

        return view('admin.commandes.show', compact('commande'));
    }

    public function recevoirCommande(Commande $commande)
    {
        if ($commande->statut !== 'expediee') {
            return back()->with('error', 'La commande doit être expédiée avant réception.');
        }

        $entrepot = $commande->entrepot;

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

            Entree::enregistrer([
                'stock_id' => $stock->id,
                'user_id' => Auth::id(),
                'commande_id' => $commande->id,
                'quantite' => $produit->pivot->quantite,
                'note' => 'Réception commande #'.$commande->id,
            ]);

            AlerteService::verifier($stock->id);
        }

        $commande->update(['statut' => 'recue']);

        // Notifier l'admin
        Auth::user()->notifierSansBloquer(new CommandeRecueNotification($commande));

        return redirect()->route('admin.commandes.index')
            ->with('success', 'Commande reçue — stock mis à jour.');
    }

    public function destroy(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Impossible de supprimer une commande en cours.');
        }
        $commande->delete();

        return redirect()->route('admin.commandes.index')
            ->with('success', 'Commande supprimée.');
    }
}
