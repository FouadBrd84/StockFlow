<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Mouvement;
use App\Models\Stock;
use App\Services\AlerteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SortieController extends Controller
{
    public function index()
    {
        $entrepot = Auth::user()->entrepot;
        $stocks = Stock::with('produit')
            ->where('entrepot_id', $entrepot->id)
            ->where('quantite', '>', 0)
            ->get();

        return view('gestionnaire.sorties.index', compact('stocks', 'entrepot'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'motif' => 'required|in:vente,casse,perte,autre',
            'note' => 'nullable|string|max:255',
        ]);

        $entrepot = Auth::user()->entrepot;

        $stock = Stock::where('produit_id', $request->produit_id)
            ->where('entrepot_id', $entrepot->id)
            ->first();

        if (! $stock || $stock->quantite < $request->quantite) {
            return back()
                ->withInput()
                ->with('error', 'Stock insuffisant. Disponible : '.($stock->quantite ?? 0));
        }

        // Décrémenter le stock
        $stock->decrement('quantite', $request->quantite);

        // Note du mouvement
        $motifs = [
            'vente' => 'Vente',
            'casse' => 'Casse',
            'perte' => 'Perte',
            'autre' => 'Autre',
        ];
        $note = $motifs[$request->motif];
        if ($request->note) {
            $note .= ' — '.$request->note;
        }

        // Enregistrer le mouvement
        Mouvement::create([
            'stock_id' => $stock->id,
            'user_id' => Auth::id(),
            'motif' => $request->motif,
            'quantite' => $request->quantite,
            'note' => $note,
        ]);

        // Vérifier les alertes
        AlerteService::verifier($stock->id);

        return redirect()->route('gestionnaire.sorties.index')
            ->with('success', 'Sortie enregistrée — stock mis à jour.');
    }
}
