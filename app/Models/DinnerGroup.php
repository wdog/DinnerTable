<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modello per i gruppi cena.
 *
 * Rappresenta un gruppo di utenti che partecipano insieme alle cene.
 * Ogni gruppo ha un codice univoco per permettere ad altri utenti di unirsi.
 */
class DinnerGroup extends Model
{
    use HasFactory;
    /**
     * Nome della tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'dinner_groups';

    /**
     * Attributi assegnabili in massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slogan',
        'group_image',
        'group_code',
        'created_by',
    ];

    /**
     * Ottiene l'utente che ha creato il gruppo.
     *
     * @return BelongsTo Relazione con il creatore del gruppo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Ottiene tutti i membri del gruppo cena.
     *
     * @return HasMany Relazione con gli utenti membri del gruppo
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'dinner_group_id');
    }
}
