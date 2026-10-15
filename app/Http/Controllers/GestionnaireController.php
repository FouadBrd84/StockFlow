<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Mouvement;
use App\Models\Stock;
use App\Models\Transfert;
use App\Services\PrevisionService;
use Illuminate\Support\Facades\Auth;

class GestionnaireController extends Controller
{
    public function dashboard(PrevisionService $previsionService)
    {
        $entrepot = Auth::user()->entrepot;

        // Pilotage : valeur du stock du magasin et prochaines ruptures prévues
        $valeurStock = $previsionService->valeurStock($entrepot->id);
        $previsions = $previsionService->pourEntrepot($entrepot->id)->take(5);

        $stockTotal = Stock::where('entrepot_id', $entrepot->id)->sum('quantite');
        $totalProduits = Stock::where('entrepot_id', $entrepot->id)->where('quantite', '>', 0)->count();
        $totalAlertes = Alerte::whereHas('stock', fn ($q) => $q->where('entrepot_id', $entrepot->id))
            ->where('statut', 'non_lue')->count();
        $transfertsEnCours = Transfert::whereHas('stockDestination', fn ($q) => $q->where('entrepot_id', $entrepot->id))
            ->where('statut', 'en_cours')->count();

        $derniersMouvements = Mouvement::with(['stock.produit', 'user'])
            ->whereHas('stock', fn ($q) => $q->where('entrepot_id', $entrepot->id))
            ->latest()->take(5)->get();

        // Graphique donut — stock par produit
        $stockParProduit = Stock::with('produit')
            ->where('entrepot_id', $entrepot->id)
            ->where('quantite', '>', 0)
            ->get()
            ->map(fn ($s) => [
                'nom' => $s->produit->nom,
                'quantite' => $s->quantite,
            ]);

        // Graphique mouvements 7 jours
        $mouvements7Jours = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d/m');

            $entrees = Mouvement::whereHas('stock', fn ($q) => $q->where('entrepot_id', $entrepot->id))
                ->whereDate('created_at', $date)
                ->whereHas('entree')->count();
            $sorties = Mouvement::whereHas('stock', fn ($q) => $q->where('entrepot_id', $entrepot->id))
                ->whereDate('created_at', $date)
                ->whereDoesntHave('entree')->count();

            $mouvements7Jours->push([
                'jour' => $label,
                'entrees' => $entrees,
                'sorties' => $sorties,
            ]);
        }

        return view('gestionnaire.dashboard', compact(
            'stockTotal', 'totalProduits', 'totalAlertes',
            'transfertsEnCours', 'derniersMouvements',
            'stockParProduit', 'mouvements7Jours',
            'valeurStock', 'previsions'
        ));
    }
}
