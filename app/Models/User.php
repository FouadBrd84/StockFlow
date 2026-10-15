<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'entrepot_id', 'must_change_password', 'telephone', 'adresse',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

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
}
