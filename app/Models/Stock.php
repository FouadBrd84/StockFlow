<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['produit_id', 'entrepot_id', 'quantite'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }

    public function isEnAlerte()
    {
        return $this->quantite <= $this->produit->seuil_alerte;
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }
}
