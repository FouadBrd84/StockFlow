<?php

namespace App\Providers;

use App\Models\Alerte;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function (\Illuminate\View\View $view): void {
            $view->with([
                'alertesNonLues' => $this->compterAlertesNonLues(),
                'messagesNonLus' => Auth::user()?->role === 'admin' ? Message::nonLus()->count() : 0,
            ]);
        });
    }

    /**
     * Alertes non lues visibles par l'utilisateur connecté : toutes pour l'admin,
     * celles de son magasin pour le gestionnaire, aucune pour le fournisseur.
     */
    private function compterAlertesNonLues(): int
    {
        $utilisateur = Auth::user();

        return match ($utilisateur?->role) {
            'admin' => Alerte::where('statut', 'non_lue')->count(),
            'gestionnaire' => Alerte::where('statut', 'non_lue')
                ->whereHas('stock', fn ($requete) => $requete->where('entrepot_id', $utilisateur->entrepot_id))
                ->count(),
            default => 0,
        };
    }
}
