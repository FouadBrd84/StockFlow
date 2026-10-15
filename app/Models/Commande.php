<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = ['fournisseur_id', 'entrepot_id', 'statut', 'note'];

    public function fournisseur()
    {
        return $this->belongsTo(User::class, 'fournisseur_id');
    }

    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'commande_produit')
            ->withPivot('quantite', 'prix_unitaire');
    }
}
