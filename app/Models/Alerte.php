<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    protected $fillable = [
        'stock_id', 'type', 'message', 'statut',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
