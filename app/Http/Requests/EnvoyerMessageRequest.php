<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnvoyerMessageRequest extends FormRequest
{
    /**
     * Seuls les gestionnaires et les fournisseurs écrivent à l'administrateur.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['gestionnaire', 'fournisseur'], true);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'sujet' => ['required', 'string', 'max:150'],
            'contenu' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sujet.required' => 'Indiquez le sujet de votre message.',
            'sujet.max' => 'Le sujet ne doit pas dépasser 150 caractères.',
            'contenu.required' => 'Écrivez votre message.',
            'contenu.min' => 'Votre message doit contenir au moins 10 caractères.',
            'contenu.max' => 'Votre message ne doit pas dépasser 3000 caractères.',
        ];
    }
}
