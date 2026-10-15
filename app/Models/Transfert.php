<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    protected $fillable = [
        'mouvement_id', 'stock_source_id', 'stock_destination_id', 'commande_id', 'statut',
    ];

    public function mouvement()
    {
        return $this->belongsTo(Mouvement::class);
    }

    public function stockSource()
    {
        return $this->belongsTo(Stock::class, 'stock_source_id');
    }

    public function stockDestination()
    {
        return $this->belongsTo(Stock::class, 'stock_destination_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Enregistre un mouvement de transfert entre deux stocks (de/vers).
     *
     * @param  array{stock_source_id:int, stock_destination_id:int, user_id:int, quantite:int, commande_id?:int|null, statut?:string, note?:string|null}  $data
     */
    public static function enregistrer(array $data): self
    {
        $mouvement = Mouvement::create([
            'stock_id' => $data['stock_destination_id'],
            'user_id' => $data['user_id'],
            'quantite' => $data['quantite'],
            'note' => $data['note'] ?? null,
        ]);

        return self::create([
            'mouvement_id' => $mouvement->id,
            'stock_source_id' => $data['stock_source_id'],
            'stock_destination_id' => $data['stock_destination_id'],
            'commande_id' => $data['commande_id'] ?? null,
            'statut' => $data['statut'] ?? 'en_cours',
        ]);
    }
}
