<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrepot;
use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        $entrepots = Entrepot::with(['stocks.produit'])->get();
        $stocks = Stock::with(['produit', 'entrepot'])->get();
        $alertes = Stock::with(['produit', 'entrepot'])
            ->whereColumn('quantite', '<=', 'produits.seuil_alerte')
            ->join('produits', 'stocks.produit_id', '=', 'produits.id')
            ->select('stocks.*')
            ->get();

        return view('admin.stocks.index', compact('entrepots', 'stocks', 'alertes'));
    }
}
