<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class FournisseurController extends Controller
{
    public function index()
    {
        $fournisseurs = User::where('role', 'fournisseur')->latest()->get();

        return view('admin.fournisseurs.index', compact('fournisseurs'));
    }

    public function create()
    {
        return view('admin.fournisseurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $fournisseur = User::create([
            'name' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'fournisseur',
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);

        \DB::table('users')->where('id', $fournisseur->id)->update(['must_change_password' => 1]);

        return redirect()->route('admin.fournisseurs.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function edit(User $fournisseur)
    {
        return view('admin.fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, User $fournisseur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($fournisseur->id)],
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        $fournisseur->update([
            'name' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);

        return redirect()->route('admin.fournisseurs.index')
            ->with('success', 'Fournisseur modifié avec succès.');
    }

    public function destroy(User $fournisseur)
    {
        $fournisseur->delete();

        return redirect()->route('admin.fournisseurs.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}
