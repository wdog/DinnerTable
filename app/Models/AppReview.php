<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Recensione dell'applicazione lasciata da un utente.
 *
 * Ogni utente può lasciare una sola recensione con voto (0-5) e commento opzionale.
 */
class AppReview extends Model
{
    protected $fillable = [
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Relazione con User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
