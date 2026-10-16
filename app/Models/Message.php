<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Message envoyé par un gestionnaire ou un fournisseur à l'administrateur.
 */
class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory;

    protected $fillable = [
        'expediteur_id', 'sujet', 'contenu', 'lu_at',
    ];

    protected function casts(): array
    {
        return [
            'lu_at' => 'datetime',
        ];
    }

    public function expediteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    /**
     * @param  Builder<Message>  $query
     */
    public function scopeNonLus(Builder $query): void
    {
        $query->whereNull('lu_at');
    }

    public function estLu(): bool
    {
        return $this->lu_at !== null;
    }
}
