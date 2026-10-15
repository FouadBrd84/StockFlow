<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Commande;
use App\Models\Entrepot;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Stats
        $totalProduits = Produit::count();
        $totalEntrepots = Entrepot::count();
        $totalFournisseurs = User::where('role', 'fournisseur')->count();
        $totalUsers = User::count();
        $totalCommandes = Commande::count();
        $totalAlertes = Alerte::where('statut', 'non_lue')->count();

        // Commandes en attente
        $commandesEnAttente = Commande::with(['fournisseur'])
            ->whereNotNull('fournisseur_id')
            ->where('statut', 'en_attente')
            ->latest()->take(5)->get();

        // Derniers mouvements
        $derniersMouvements = Mouvement::with(['stock.produit', 'user', 'entree', 'transfert'])
            ->latest()->take(5)->get();

        // Graphique 1 — Stock par entrepôt
        $stockParEntrepot = Entrepot::select('entrepots.nom')
            ->selectRaw('COALESCE(SUM(stocks.quantite), 0) as total')
            ->leftJoin('stocks', 'entrepots.id', '=', 'stocks.entrepot_id')
            ->groupBy('entrepots.id', 'entrepots.nom')
            ->get();

        // Graphique 2 — Mouvements 7 derniers jours
        $mouvements7Jours = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d/m');

            $entrees = Mouvement::whereDate('created_at', $date)
                ->whereHas('entree')->count();
            $sorties = Mouvement::whereDate('created_at', $date)
                ->whereDoesntHave('entree')->count();

            $mouvements7Jours->push([
                'jour' => $label,
                'entrees' => $entrees,
                'sorties' => $sorties,
            ]);
        }

        return view('admin.dashboard', compact(
            'totalProduits', 'totalEntrepots', 'totalFournisseurs',
            'totalUsers', 'totalCommandes', 'totalAlertes',
            'commandesEnAttente', 'derniersMouvements',
            'stockParEntrepot', 'mouvements7Jours'
        ));
    }
}
