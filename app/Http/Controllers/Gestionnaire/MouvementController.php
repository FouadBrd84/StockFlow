<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Mouvement;
use Illuminate\Support\Facades\Auth;

class MouvementController extends Controller
{
    public function index()
    {
        $entrepot = Auth::user()->entrepot;
        $mouvements = Mouvement::with(['stock.produit', 'user', 'entree', 'transfert'])
            ->whereHas('stock', fn ($q) => $q->where('entrepot_id', $entrepot->id))
            ->latest()->paginate(20);

        return view('gestionnaire.mouvements.index', compact('mouvements', 'entrepot'));
    }
}
