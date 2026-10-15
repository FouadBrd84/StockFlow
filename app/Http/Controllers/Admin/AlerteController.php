<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alerte;

class AlerteController extends Controller
{
    public function index()
    {
        $alertes = Alerte::with(['stock.produit', 'stock.entrepot'])
            ->latest()->get();

        return view('admin.alertes.index', compact('alertes'));
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
