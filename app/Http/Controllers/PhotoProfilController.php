<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoProfilRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Photos de profil, stockées dans un dossier privé (storage/app/private/avatars)
 * et servies uniquement aux utilisateurs connectés.
 */
class PhotoProfilController extends Controller
{
    public function update(PhotoProfilRequest $request): RedirectResponse
    {
        $utilisateur = $request->user();
        $anciennePhoto = $utilisateur->photo;

        $utilisateur->update([
            'photo' => $request->file('photo')->store('avatars', 'local'),
        ]);

        if ($anciennePhoto) {
            Storage::disk('local')->delete($anciennePhoto);
        }

        return redirect()->route('profil.edit')->with('success', 'Photo de profil mise à jour 📸');
    }

    public function destroy(): RedirectResponse
    {
        $utilisateur = Auth::user();

        if ($utilisateur->photo) {
            Storage::disk('local')->delete($utilisateur->photo);
            $utilisateur->update(['photo' => null]);
        }

        return redirect()->route('profil.edit')->with('success', 'Photo de profil supprimée.');
    }

    public function show(User $user): StreamedResponse
    {
        abort_unless($user->photo && Storage::disk('local')->exists($user->photo), 404);

        return Storage::disk('local')->response($user->photo, null, [
            'Cache-Control' => 'private, max-age=604800',
        ]);
    }
}
