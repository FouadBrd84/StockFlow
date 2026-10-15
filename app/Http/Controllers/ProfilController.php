<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('profil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password_actuel' => 'required',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->mixedCase()->numbers()->symbols(),
                ],
            ], [
                'password_actuel.required' => 'Veuillez entrer votre mot de passe actuel.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            ]);

            // Vérifier le mot de passe actuel
            if (! Hash::check($request->password_actuel, $user->password)) {
                return back()->withErrors(['password_actuel' => 'Mot de passe actuel incorrect.']);
            }

            // Vérifier que le nouveau est différent
            if (Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'ancien.']);
            }

            \DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('profil.edit')
            ->with('success', 'Profil mis à jour avec succès !');
    }
}
