<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrepot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('entrepot')->latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Bloquer la création d'un admin
        if ($request->role === 'admin') {
            return back()->with('error', 'Impossible de créer un autre compte admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:gestionnaire,fournisseur',
            'type' => 'nullable|in:city,express,market,hypermarket,bio,drive,proximite,flash',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string',
        ]);

        // Si c'est un gestionnaire — créer l'entrepôt automatiquement
        $entrepotId = null;
        if ($request->role === 'gestionnaire') {
            $entrepot = Entrepot::create([
                'nom' => $request->name,
                'type' => $request->type,
                'adresse' => $request->adresse,
                'telephone' => $request->telephone,
            ]);
            $entrepotId = $entrepot->id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'entrepot_id' => $entrepotId,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);

        // Forcer must_change_password via DB directement
        \DB::table('users')->where('id', $user->id)->update(['must_change_password' => 1]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé — il devra changer son mot de passe à la première connexion.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Bloquer le changement de rôle vers admin
        if ($request->role === 'admin') {
            return back()->with('error', 'Impossible d\'assigner le rôle admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:gestionnaire,fournisseur',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            // Valider les exigences du mot de passe
            $request->validate([
                'password' => [
                    'confirmed',
                    'min:8',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ]);
            $data['password'] = Hash::make($request->password);
            // Admin a changé le mot de passe → obliger l'utilisateur à le changer
            \DB::table('users')->where('id', $user->id)->update(['must_change_password' => 1]);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $user)
    {
        // Empêcher la suppression de l'admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Impossible de supprimer le compte admin.');
        }
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}
