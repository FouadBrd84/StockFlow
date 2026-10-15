<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $entrepot = Auth::user()->entrepot;
        $stocks = Stock::with('produit')
            ->where('entrepot_id', $entrepot->id)
            ->get();

        return view('gestionnaire.stocks.index', compact('entrepot', 'stocks'));
    }
}
