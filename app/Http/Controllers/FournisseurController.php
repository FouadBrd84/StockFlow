<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Support\Facades\Auth;

class FournisseurController extends Controller
{
    public function dashboard()
    {
        $fournisseurId = Auth::id();

        return view('fournisseur.dashboard', [
            'commandesEnAttente' => Commande::where('fournisseur_id', $fournisseurId)
                ->where('statut', 'en_attente')->count(),
            'commandesConfirmees' => Commande::where('fournisseur_id', $fournisseurId)
                ->where('statut', 'confirmee')->count(),
            'commandesExpediees' => Commande::where('fournisseur_id', $fournisseurId)
                ->where('statut', 'expediee')->count(),
            'commandesRecues' => Commande::where('fournisseur_id', $fournisseurId)
                ->where('statut', 'recue')->count(),
            'dernieresCommandes' => Commande::with(['entrepot', 'produits'])
                ->where('fournisseur_id', $fournisseurId)
                ->latest()->take(5)->get(),
        ]);
    }
}
