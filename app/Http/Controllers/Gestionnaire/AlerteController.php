<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Alerte;
use Illuminate\Support\Facades\Auth;

class AlerteController extends Controller
{
    public function index()
    {
        $entrepot = Auth::user()->entrepot;
        $alertes = Alerte::with(['stock.produit', 'stock.entrepot'])
            ->whereHas('stock', fn ($q) => $q->where('entrepot_id', $entrepot->id))
            ->latest()->get();

        return view('gestionnaire.alertes.index', compact('alertes'));
    }

    public function marquerLue(Alerte $alerte)
    {
        $alerte->update(['statut' => 'lue']);

        return back()->with('success', 'Alerte marquée comme lue.');
    }

    public function marquerTraitee(Alerte $alerte)
    {
        $alerte->update(['statut' => 'traitee']);

        return back()->with('success', 'Alerte marquée comme traitée.');
    }
}
