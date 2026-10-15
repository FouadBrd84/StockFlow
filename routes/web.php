<?php

use App\Http\Controllers\Admin\AlerteController;
use App\Http\Controllers\Admin\CommandeController as AdminCommandeController;
use App\Http\Controllers\Admin\MouvementController;
use App\Http\Controllers\Admin\ProduitController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TransfertController as AdminTransfertController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Fournisseur\CommandeController as FournisseurCommandeController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\Gestionnaire\CommandeController as GestionnaireCommandeController;
use App\Http\Controllers\Gestionnaire\SortieController;
use App\Http\Controllers\GestionnaireController;
use App\Http\Controllers\ProfilController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->role === 'gestionnaire') {
            return redirect()->route('gestionnaire.dashboard');
        }
        if (auth()->user()->role === 'fournisseur') {
            return redirect()->route('fournisseur.dashboard');
        }
    })->name('dashboard');

    // Profil
    Route::get('/profil', [ProfilController::class, 'edit'])
        ->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])
        ->name('profil.update');

    // ══════════════════════════════════════
    // Routes Admin
    // ══════════════════════════════════════
    Route::middleware('role:admin')->group(function () {

        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/admin/stocks', [StockController::class, 'index'])
            ->name('admin.stocks.index');

        Route::get('/admin/alertes', [AlerteController::class, 'index'])
            ->name('admin.alertes.index');
        Route::post('/admin/alertes/{alerte}/lue', [AlerteController::class, 'marquerLue'])
            ->name('admin.alertes.lue');
        Route::post('/admin/alertes/{alerte}/traitee', [AlerteController::class, 'marquerTraitee'])
            ->name('admin.alertes.traitee');

        Route::get('/admin/mouvements', [MouvementController::class, 'index'])
            ->name('admin.mouvements.index');
        Route::get('/admin/mouvements/export-pdf', [MouvementController::class, 'exportPdf'])
            ->name('admin.mouvements.export-pdf');

        Route::get('/admin/produits-fournisseur/{fournisseur}', function (User $fournisseur) {
            return response()->json($fournisseur->produits);
        })->name('admin.produits.fournisseur');

        Route::get('/admin/produits-par-fournisseur/{fournisseur}', function (User $fournisseur) {
            return response()->json($fournisseur->produits);
        })->name('admin.mouvements.produits');

        Route::resource('admin/fournisseurs', App\Http\Controllers\Admin\FournisseurController::class)
            ->names([
                'index' => 'admin.fournisseurs.index',
                'create' => 'admin.fournisseurs.create',
                'store' => 'admin.fournisseurs.store',
                'show' => 'admin.fournisseurs.show',
                'edit' => 'admin.fournisseurs.edit',
                'update' => 'admin.fournisseurs.update',
                'destroy' => 'admin.fournisseurs.destroy',
            ]);

        Route::resource('admin/produits', ProduitController::class)
            ->names([
                'index' => 'admin.produits.index',
                'create' => 'admin.produits.create',
                'store' => 'admin.produits.store',
                'show' => 'admin.produits.show',
                'edit' => 'admin.produits.edit',
                'update' => 'admin.produits.update',
                'destroy' => 'admin.produits.destroy',
            ]);

        Route::resource('admin/users', UserController::class)
            ->names([
                'index' => 'admin.users.index',
                'create' => 'admin.users.create',
                'store' => 'admin.users.store',
                'show' => 'admin.users.show',
                'edit' => 'admin.users.edit',
                'update' => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]);

        Route::get('/admin/commandes', [AdminCommandeController::class, 'index'])
            ->name('admin.commandes.index');
        Route::get('/admin/commandes/create', [AdminCommandeController::class, 'create'])
            ->name('admin.commandes.create');
        Route::post('/admin/commandes', [AdminCommandeController::class, 'store'])
            ->name('admin.commandes.store');
        Route::get('/admin/commandes/{commande}', [AdminCommandeController::class, 'show'])
            ->name('admin.commandes.show');
        Route::post('/admin/commandes/{commande}/recevoir', [AdminCommandeController::class, 'recevoirCommande'])
            ->name('admin.commandes.recevoir');
        Route::delete('/admin/commandes/{commande}', [AdminCommandeController::class, 'destroy'])
            ->name('admin.commandes.destroy');

        Route::get('/admin/magasins', [AdminTransfertController::class, 'index'])
            ->name('admin.magasins.index');
        Route::post('/admin/magasins/{commande}/valider', [AdminTransfertController::class, 'validerCommande'])
            ->name('admin.commandes-magasin.valider');
    });

    // ══════════════════════════════════════
    // Routes Gestionnaire
    // ══════════════════════════════════════
    Route::middleware('role:gestionnaire')->group(function () {

        Route::get('/gestionnaire/dashboard', [GestionnaireController::class, 'dashboard'])
            ->name('gestionnaire.dashboard');

        Route::get('/gestionnaire/stocks', [App\Http\Controllers\Gestionnaire\StockController::class, 'index'])
            ->name('gestionnaire.stocks.index');

        Route::get('/gestionnaire/alertes', [App\Http\Controllers\Gestionnaire\AlerteController::class, 'index'])
            ->name('gestionnaire.alertes.index');
        Route::post('/gestionnaire/alertes/{alerte}/lue', [App\Http\Controllers\Gestionnaire\AlerteController::class, 'marquerLue'])
            ->name('gestionnaire.alertes.lue');
        Route::post('/gestionnaire/alertes/{alerte}/traitee', [App\Http\Controllers\Gestionnaire\AlerteController::class, 'marquerTraitee'])
            ->name('gestionnaire.alertes.traitee');

        Route::get('/gestionnaire/mouvements', [App\Http\Controllers\Gestionnaire\MouvementController::class, 'index'])
            ->name('gestionnaire.mouvements.index');

        Route::get('/gestionnaire/sorties', [SortieController::class, 'index'])
            ->name('gestionnaire.sorties.index');
        Route::post('/gestionnaire/sorties', [SortieController::class, 'store'])
            ->name('gestionnaire.sorties.store');

        Route::get('/gestionnaire/commandes', [GestionnaireCommandeController::class, 'index'])
            ->name('gestionnaire.commandes.index');
        Route::get('/gestionnaire/commandes/create', [GestionnaireCommandeController::class, 'create'])
            ->name('gestionnaire.commandes.create');
        Route::post('/gestionnaire/commandes', [GestionnaireCommandeController::class, 'store'])
            ->name('gestionnaire.commandes.store');
        Route::get('/gestionnaire/commandes/{commande}', [GestionnaireCommandeController::class, 'show'])
            ->name('gestionnaire.commandes.show');
        Route::post('/gestionnaire/commandes/{commande}/recevoir', [GestionnaireCommandeController::class, 'recevoir'])
            ->name('gestionnaire.commandes.recevoir');
    });

    // ══════════════════════════════════════
    // Routes Fournisseur
    // ══════════════════════════════════════
    Route::middleware('role:fournisseur')->group(function () {

        Route::get('/fournisseur/dashboard', [FournisseurController::class, 'dashboard'])
            ->name('fournisseur.dashboard');

        Route::get('/fournisseur/commandes', [FournisseurCommandeController::class, 'index'])
            ->name('fournisseur.commandes.index');
        Route::post('/fournisseur/commandes/{commande}/confirmer', [FournisseurCommandeController::class, 'confirmer'])
            ->name('fournisseur.commandes.confirmer');
        Route::post('/fournisseur/commandes/{commande}/expedier', [FournisseurCommandeController::class, 'expedier'])
            ->name('fournisseur.commandes.expedier');

        Route::resource('fournisseur/produits', App\Http\Controllers\Fournisseur\ProduitController::class)
            ->names([
                'index' => 'fournisseur.produits.index',
                'create' => 'fournisseur.produits.create',
                'store' => 'fournisseur.produits.store',
                'edit' => 'fournisseur.produits.edit',
                'update' => 'fournisseur.produits.update',
                'destroy' => 'fournisseur.produits.destroy',
            ]);
    });

}); // ← fermeture du groupe auth global

// Changement de mot de passe obligatoire
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', function () {
        return view('auth.change-password');
    })->name('password.change');

    Route::post('/password/change', function (Request $request) {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*#?&).',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        if (Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'ancien.']);
        }

        DB::table('users')
            ->where('id', auth()->id())
            ->update([
                'password' => Hash::make($request->password),
                'must_change_password' => 0,
            ]);

        return redirect()->route('dashboard')->with('success', 'Mot de passe changé avec succès !');
    })->name('password.change.update');
});

require __DIR__.'/auth.php';
