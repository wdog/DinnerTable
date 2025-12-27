<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modello per il profilo utente.
 *
 * Contiene le informazioni aggiuntive dell'utente oltre ai dati di autenticazione:
 * indirizzo, capacità di ospitare, accettazione privacy e avatar.
 *
 * Funzionalità principali:
 * - Validazione completamento profilo
 * - Gestione indirizzo completo
 * - Tracciamento accettazione privacy
 * - Gestione avatar utente
 *
 * @see User Modello utente associato
 */
class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'city',
        'address',
        'house_number',
        'postal_code',
        'max_guests',
        'privacy_accepted_at',
        'avatar_url',
    ];

    /**
     * Relazione con l'utente proprietario del profilo.
     *
     * Ogni profilo appartiene a un singolo utente.
     *
     * @return BelongsTo Relazione con il modello User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se il profilo è completo.
     *
     * Controlla che tutti i campi obbligatori siano stati compilati:
     * - Indirizzo completo (città, via, numero civico, CAP)
     * - Numero massimo di ospiti
     * - Accettazione della privacy
     *
     * @return bool True se il profilo è completo, false altrimenti
     */
    public function isComplete(): bool
    {
        return ! empty($this->city) &&
            ! empty($this->address) &&
            ! empty($this->house_number) &&
            ! empty($this->postal_code) &&
            ! empty($this->max_guests) &&
            ! is_null($this->privacy_accepted_at);
    }

    /**
     * Accessor per l'indirizzo completo formattato.
     *
     * Combina tutti i campi dell'indirizzo in una stringa formattata:
     * "Via, Numero, Città, CAP"
     *
     * @return Attribute Attributo computed con l'indirizzo completo
     */
    public function fullAddress(): Attribute
    {
        return new Attribute(
            get: fn () => $this->address . ', ' . $this->house_number . ', ' . $this->city . ', ' . $this->postal_code
        );
    }

    protected function casts(): array
    {
        return [
            'privacy_accepted_at' => 'datetime',
            'max_guests'          => 'integer',
        ];
    }
}
