<?php

namespace App\Services;

use App\Models\Mouvement;
use App\Models\Stock;
use App\Models\Transfert;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Prévision des ruptures de stock à partir du rythme de consommation récent.
 *
 * Consommation d'un stock = ses sorties (vente, casse, perte) + les transferts qui partent de lui
 * (pour le dépôt central), sur une fenêtre glissante.
 */
class PrevisionService
{
    /** Nombre de jours de couverture visés quand on propose une quantité à recommander. */
    public const JOURS_COUVERTURE = 14;

    /**
     * Stocks qui consomment, triés du plus urgent au moins urgent.
     *
     * @return Collection<int, array{stock_id: int, produit: string, entrepot: string, quantite: int, seuil: int, consommation_jour: float, jours_restants: int|null, date_rupture: Carbon|null, a_commander: int, niveau: string}>
     */
    public function pourEntrepot(?int $entrepotId = null, int $jours = 30): Collection
    {
        $depuis = now()->subDays($jours);
        $consommations = $this->consommationsDepuis($depuis);

        if ($consommations->isEmpty()) {
            return collect();
        }

        return Stock::with(['produit:id,nom,seuil_alerte', 'entrepot:id,nom'])
            ->whereIn('id', $consommations->keys())
            ->when($entrepotId, fn ($requete) => $requete->where('entrepot_id', $entrepotId))
            ->get()
            ->map(fn (Stock $stock) => $this->prevoir($stock, $consommations->get($stock->id) / $jours))
            ->sortBy(fn (array $prevision) => $prevision['jours_restants'] ?? PHP_INT_MAX)
            ->values();
    }

    /**
     * Valeur marchande du stock (quantité × prix du produit), pour un entrepôt ou tout le réseau.
     */
    public function valeurStock(?int $entrepotId = null): float
    {
        return (float) Stock::query()
            ->join('produits', 'produits.id', '=', 'stocks.produit_id')
            ->when($entrepotId, fn ($requete) => $requete->where('stocks.entrepot_id', $entrepotId))
            ->sum(DB::raw('stocks.quantite * produits.prix'));
    }

    /**
     * Quantité consommée par stock depuis une date : sorties + transferts sortants.
     *
     * @return Collection<int, int>
     */
    private function consommationsDepuis(Carbon $depuis): Collection
    {
        $sorties = Mouvement::query()
            ->whereDoesntHave('entree')
            ->whereDoesntHave('transfert')
            ->where('created_at', '>=', $depuis)
            ->whereNotNull('stock_id')
            ->groupBy('stock_id')
            ->selectRaw('stock_id, SUM(quantite) as total')
            ->pluck('total', 'stock_id');

        $transfertsSortants = Transfert::query()
            ->join('mouvements', 'mouvements.id', '=', 'transferts.mouvement_id')
            ->where('mouvements.created_at', '>=', $depuis)
            ->groupBy('transferts.stock_source_id')
            ->selectRaw('transferts.stock_source_id, SUM(mouvements.quantite) as total')
            ->pluck('total', 'stock_source_id');

        return $sorties->keys()->merge($transfertsSortants->keys())->unique()
            ->mapWithKeys(fn ($stockId) => [
                (int) $stockId => (int) ($sorties->get($stockId, 0) + $transfertsSortants->get($stockId, 0)),
            ])
            ->filter(fn (int $total) => $total > 0);
    }

    /**
     * @return array{stock_id: int, produit: string, entrepot: string, quantite: int, seuil: int, consommation_jour: float, jours_restants: int|null, date_rupture: Carbon|null, a_commander: int, niveau: string}
     */
    private function prevoir(Stock $stock, float $consommationJour): array
    {
        $joursRestants = $consommationJour > 0 ? (int) floor($stock->quantite / $consommationJour) : null;
        $seuil = (int) $stock->produit->seuil_alerte;
        $besoin = (int) ceil($consommationJour * self::JOURS_COUVERTURE) + $seuil - $stock->quantite;

        return [
            'stock_id' => $stock->id,
            'produit' => $stock->produit->nom,
            'entrepot' => $stock->entrepot->nom,
            'quantite' => (int) $stock->quantite,
            'seuil' => $seuil,
            'consommation_jour' => round($consommationJour, 1),
            'jours_restants' => $joursRestants,
            'date_rupture' => $joursRestants !== null ? now()->addDays($joursRestants) : null,
            'a_commander' => max(0, $besoin),
            'niveau' => match (true) {
                $joursRestants !== null && $joursRestants <= 3 => 'critique',
                $joursRestants !== null && $joursRestants <= 7 => 'attention',
                default => 'ok',
            },
        ];
    }
}
