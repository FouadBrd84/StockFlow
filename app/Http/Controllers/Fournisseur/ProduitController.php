<?php

namespace App\Http\Controllers\Fournisseur;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::where('fournisseur_id', Auth::id())->latest()->get();

        return view('fournisseur.produits.index', compact('produits'));
    }

    public function create()
    {
        return view('fournisseur.produits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'description' => 'nullable',
            'prix' => 'required|numeric|min:0',
            'unite' => 'required',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        Produit::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'unite' => $request->unite,
            'seuil_alerte' => $request->seuil_alerte,
            'fournisseur_id' => Auth::id(),
        ]);

        return redirect()->route('fournisseur.produits.index')
            ->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(Produit $produit)
    {
        if ($produit->fournisseur_id !== Auth::id()) {
            abort(403);
        }

        return view('fournisseur.produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        if ($produit->fournisseur_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nom' => 'required',
            'description' => 'nullable',
            'prix' => 'required|numeric|min:0',
            'unite' => 'required',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        $produit->update($request->all());

        return redirect()->route('fournisseur.produits.index')
            ->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(Produit $produit)
    {
        if ($produit->fournisseur_id !== Auth::id()) {
            abort(403);
        }

        $produit->delete();

        return redirect()->route('fournisseur.produits.index')
            ->with('success', 'Produit supprimé.');
    }
}
