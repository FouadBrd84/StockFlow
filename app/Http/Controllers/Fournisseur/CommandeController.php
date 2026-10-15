<?php

namespace App\Http\Controllers\Fournisseur;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\User;
use App\Notifications\CommandeExpedieeNotification;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['entrepot', 'produits'])
            ->where('fournisseur_id', Auth::id())
            ->latest()
            ->get();

        return view('fournisseur.commandes.index', compact('commandes'));
    }

    public function confirmer(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Cette commande ne peut pas être confirmée.');
        }

        $commande->update(['statut' => 'confirmee']);

        return back()->with('success', 'Commande confirmée.');
    }

    public function expedier(Commande $commande)
    {
        if ($commande->statut !== 'confirmee') {
            return back()->with('error', 'La commande doit être confirmée avant expédition.');
        }

        $commande->update(['statut' => 'expediee']);

        // Notifier l'admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notifierSansBloquer(new CommandeExpedieeNotification($commande));
        }

        return back()->with('success', 'Commande marquée comme expédiée.');
    }
}
