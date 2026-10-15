<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = [
        'stock_id', 'user_id', 'quantite', 'motif', 'note',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entree()
    {
        return $this->hasOne(Entree::class);
    }

    public function transfert()
    {
        return $this->hasOne(Transfert::class);
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->entree ? 'entree' : ($this->transfert ? 'transfert' : 'sortie'),
        );
    }
}
