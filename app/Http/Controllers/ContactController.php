<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnvoyerMessageRequest;
use App\Models\User;
use App\Notifications\MessageAdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Formulaire « Contacter l'admin » + derniers messages envoyés.
     */
    public function create(): View
    {
        abort_unless(in_array(Auth::user()->role, ['gestionnaire', 'fournisseur'], true), 403);

        $messagesEnvoyes = Auth::user()->messagesEnvoyes()->latest()->take(5)->get();

        return view('contact.create', compact('messagesEnvoyes'));
    }

    /**
     * Enregistre le message puis prévient l'admin par email (sans bloquer si l'envoi échoue).
     */
    public function store(EnvoyerMessageRequest $request): RedirectResponse
    {
        $message = $request->user()->messagesEnvoyes()->create($request->validated());

        User::where('role', 'admin')->first()?->notifierSansBloquer(new MessageAdminNotification($message));

        return redirect()->route('contact.create')
            ->with('success', 'Message envoyé à l\'administrateur ✉️');
    }
}
