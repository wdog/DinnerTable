<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modello per le date delle cene.
 *
 * Rappresenta una specifica data in cui il gruppo può organizzare una cena.
 * Ogni data può avere multiple disponibilità da parte dei membri del gruppo.
 *
 * Funzionalità principali:
 * - Associazione a un gruppo cena specifico
 * - Gestione delle disponibilità per quella data
 * - Cast automatico della data
 *
 * @see DinnerGroup Gruppo cena associato
 * @see DinnerAvailability Disponibilità per questa data
 */
class DinnerDate extends Model
{
    use HasFactory;
    protected $fillable = [
        'dinner_group_id',
        'dinner_date',
    ];

    protected $casts = [
        'dinner_date' => 'date',
    ];

    /**
     * Relazione con il gruppo cena associato.
     *
     * Ogni data cena appartiene a un specifico gruppo.
     *
     * @return BelongsTo Relazione con il modello DinnerGroup
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(DinnerGroup::class, 'dinner_group_id');
    }

    /**
     * Relazione con le disponibilità dichiarate per questa data.
     *
     * Ottiene tutte le disponibilità (host e guest) che i membri
     * del gruppo hanno dichiarato per questa specifica data.
     *
     * @return HasMany Relazione con il modello DinnerAvailability
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(DinnerAvailability::class);
    }
}
