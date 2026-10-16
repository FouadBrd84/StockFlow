<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'entrepot_id', 'must_change_password', 'telephone', 'adresse', 'photo',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
/**
     * hachage mot de passe .
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Envoie une notification sans bloquer l'action métier si l'envoi échoue (ex. SMTP injoignable).
     */
    public function notifierSansBloquer(Notification $notification): void
    {
        try {
            $this->notify($notification);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'fournisseur_id');
    }

    public function commandesFournisseur()
    {
        return $this->hasMany(Commande::class, 'fournisseur_id');
    }

    public function messagesEnvoyes(): HasMany
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    /**
     * Adresse de la photo de profil (servie par une route réservée aux utilisateurs connectés),
     * ou null si l'utilisateur n'a pas de photo. Le paramètre « v » force le rafraîchissement après un changement.
     */
    public function urlPhoto(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return route('photos.show', ['user' => $this, 'v' => substr(md5($this->photo), 0, 8)]);
    }

    /**
     * Initiale affichée quand l'utilisateur n'a pas de photo.
     */
    public function initiale(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 1));
    }
}
