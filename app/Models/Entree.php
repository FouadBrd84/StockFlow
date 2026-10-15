<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entree extends Model
{
    protected $fillable = ['mouvement_id', 'commande_id'];

    public function mouvement()
    {
        return $this->belongsTo(Mouvement::class);
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Enregistre un mouvement d'entrée et incrémente le stock concerné.
     *
     * @param  array{stock_id:int, user_id:int, quantite:int, commande_id?:int|null, note?:string|null}  $data
     */
    public static function enregistrer(array $data): self
    {
        $mouvement = Mouvement::create([
            'stock_id' => $data['stock_id'],
            'user_id' => $data['user_id'],
            'quantite' => $data['quantite'],
            'note' => $data['note'] ?? null,
        ]);

        return self::create([
            'mouvement_id' => $mouvement->id,
            'commande_id' => $data['commande_id'] ?? null,
        ]);
    }
}
