<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Entrepot;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'role' => ['required', 'in:gestionnaire,fournisseur'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse' => ['required', 'string', 'max:255'],
        ];

        if ($request->role === 'gestionnaire') {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['type_entrepot'] = ['required', 'in:city,express,market,hypermarket,bio,drive,proximite,flash'];
        }

        if ($request->role === 'fournisseur') {
            $rules['nom'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules);

        if ($request->role === 'gestionnaire') {

            $entrepot = Entrepot::create([
                'nom' => $request->name,
                'type' => $request->type_entrepot,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'gestionnaire',
                'entrepot_id' => $entrepot->id,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
            ]);

        } else {

            $user = User::create([
                'name' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'fournisseur',
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
