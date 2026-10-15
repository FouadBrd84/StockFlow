<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrepot;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::with(['fournisseur'])->latest()->get();

        return view('admin.produits.index', compact('produits'));
    }

    public function create()
    {
        $fournisseurs = User::where('role', 'fournisseur')->get();

        return view('admin.produits.create', compact('fournisseurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'description' => 'nullable',
            'prix' => 'required|numeric',
            'unite' => 'required',
            'seuil_alerte' => 'required|integer|min:0',
            'fournisseur_id' => ['required', Rule::exists('users', 'id')->where('role', 'fournisseur')],
            'quantite_init' => 'required|integer|min:0',
        ]);

        $produit = Produit::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'unite' => $request->unite,
            'seuil_alerte' => $request->seuil_alerte,
            'fournisseur_id' => $request->fournisseur_id,
        ]);

        // Créer le stock dans l'entrepôt central
        $central = Entrepot::where('type', 'central')->first();
        if ($central) {
            Stock::create([
                'produit_id' => $produit->id,
                'entrepot_id' => $central->id,
                'quantite' => $request->quantite_init,
            ]);
        }

        return redirect()->route('admin.produits.index')
            ->with('success', 'Produit créé avec succès.');
    }

    public function edit(Produit $produit)
    {
        $fournisseurs = User::where('role', 'fournisseur')->get();

        return view('admin.produits.edit', compact('produit', 'fournisseurs'));
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom' => 'required',
            'description' => 'nullable',
            'prix' => 'required|numeric',
            'unite' => 'required',
            'seuil_alerte' => 'required|integer|min:0',
            'fournisseur_id' => ['required', Rule::exists('users', 'id')->where('role', 'fournisseur')],
        ]);

        $produit->update($request->all());

        return redirect()->route('admin.produits.index')
            ->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();

        return redirect()->route('admin.produits.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
