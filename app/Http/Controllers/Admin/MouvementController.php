<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrepot;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class MouvementController extends Controller
{
    private function buildQuery()
    {
        $query = Mouvement::with([
            'stock.produit', 'stock.entrepot', 'user',
            'entree.commande.fournisseur',
            'transfert.stockSource.entrepot', 'transfert.stockDestination.entrepot',
        ]);

        if (request('type') === 'entree') {
            $query->whereHas('entree');
        } elseif (request('type') === 'sortie') {
            $query->whereDoesntHave('entree');
        }

        if (request('fournisseurs') && count(request('fournisseurs')) > 0) {
            $query->whereHas('entree.commande', function ($q) {
                $q->whereIn('fournisseur_id', request('fournisseurs'));
            });
        }

        if (request('entrepot_dest_id')) {
            $query->whereHas('stock', function ($q) {
                $q->where('entrepot_id', request('entrepot_dest_id'));
            });
        }

        if (request('produits') && count(request('produits')) > 0) {
            $query->whereHas('stock', function ($q) {
                $q->whereIn('produit_id', request('produits'));
            });
        }

        return $query;
    }

    public function index()
    {
        $mouvements = $this->buildQuery()->latest()->paginate(20)->withQueryString();
        $fournisseurs = User::where('role', 'fournisseur')->get();
        $entrepots = Entrepot::where('type', '!=', 'central')->get();
        $produits = Produit::all();
        $produitsFiltres = collect();

        if (request('fournisseurs') && count(request('fournisseurs')) > 0) {
            $produitsFiltres = Produit::whereIn('fournisseur_id', request('fournisseurs'))->get();
        }

        return view('admin.mouvements.index', compact(
            'mouvements', 'fournisseurs', 'entrepots', 'produits', 'produitsFiltres'
        ));
    }

    public function exportPdf()
    {
        $mouvements = $this->buildQuery()->latest()->get();

        $pdf = Pdf::loadView('admin.mouvements.pdf', compact('mouvements'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('historique_mouvements_'.now()->format('d-m-Y').'.pdf');
    }
}
