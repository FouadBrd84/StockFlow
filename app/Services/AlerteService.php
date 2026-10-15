<?php

namespace App\Services;

use App\Models\Alerte;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\StockBasNotification;

class AlerteService
{
    public static function verifier(int $stockId): void
    {
        $stock = Stock::with('produit', 'entrepot')->find($stockId);

        if (! $stock) {
            return;
        }

        $produit = $stock->produit;
        $entrepot = $stock->entrepot;

        if ($stock->quantite == 0) {
            self::creerAlerte($stock->id, 'rupture',
                "Rupture de stock : {$produit->nom} — Stock : 0");
            self::envoyerEmailStockBas($stock, $produit, $entrepot, 0);
        } elseif ($stock->quantite <= $produit->seuil_alerte) {
            self::creerAlerte($stock->id, 'stock_bas',
                "Stock bas : {$produit->nom} — Stock : {$stock->quantite} / Seuil : {$produit->seuil_alerte}");
            self::envoyerEmailStockBas($stock, $produit, $entrepot, $stock->quantite);
        }
    }

    private static function envoyerEmailStockBas($stock, $produit, $entrepot, $quantite): void
    {
        // Notifier le gestionnaire de l'entrepôt
        $gestionnaire = User::where('entrepot_id', $entrepot->id)
            ->where('role', 'gestionnaire')
            ->first();

        if ($gestionnaire) {
            $gestionnaire->notifierSansBloquer(new StockBasNotification(
                $produit->nom,
                $quantite,
                $produit->seuil_alerte,
                $entrepot->nom
            ));
        }

        // Notifier l'admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notifierSansBloquer(new StockBasNotification(
                $produit->nom,
                $quantite,
                $produit->seuil_alerte,
                $entrepot->nom
            ));
        }
    }

    private static function creerAlerte(int $stockId, string $type, string $message): void
    {
        $existe = Alerte::where('stock_id', $stockId)
            ->where('type', $type)
            ->whereIn('statut', ['non_lue', 'lue'])
            ->exists();

        if (! $existe) {
            Alerte::create([
                'stock_id' => $stockId,
                'type' => $type,
                'message' => $message,
                'statut' => 'non_lue',
            ]);
        }
    }
}
