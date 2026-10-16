<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Boîte de réception de l'administrateur.
     */
    public function index(): View
    {
        $messages = Message::with('expediteur.entrepot')->latest()->paginate(15);
        $nonLus = Message::nonLus()->count();

        return view('admin.messages.index', compact('messages', 'nonLus'));
    }

    public function marquerLu(Message $message): RedirectResponse
    {
        if (! $message->estLu()) {
            $message->update(['lu_at' => now()]);
        }

        return back()->with('success', 'Message marqué comme lu.');
    }
}
