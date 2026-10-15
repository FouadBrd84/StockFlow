<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'description', 'prix', 'unite', 'seuil_alerte', 'fournisseur_id',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(User::class, 'fournisseur_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'commande_produit')
            ->withPivot('quantite', 'prix_unitaire');
    }
}
