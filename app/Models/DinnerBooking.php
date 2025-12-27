<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\DinnerBookingObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * Modello per le prenotazioni delle cene.
 *
 * Rappresenta una prenotazione effettuata da un utente (guest) per partecipare
 * a una cena ospitata da un altro membro del gruppo (host).
 *
 * Funzionalità principali:
 * - Gestione dello stato della prenotazione (confermata, in attesa, cancellata)
 * - Calcolo automatico del totale ospiti (guest + accompagnatori)
 * - Validazione della capacità disponibile presso l'host
 * - Tracciamento degli oggetti portati dal guest
 *
 * @see DinnerBookingObserver Observer per logica automatica
 * @see DinnerBookingStatus Enum per gli stati possibili
 * @see DinnerAvailability Disponibilità dell'host associata
 */
#[ObservedBy([DinnerBookingObserver::class])]
class DinnerBooking extends Model
{
    protected $fillable = [
        'host_availability_id',
        'guest_user_id',
        'guests_count',
        'bringing_items',
        'notes',
        'status',
    ];

    protected $casts = [
        'guests_count'   => 'integer',
        'bringing_items' => 'array', // tags
        'status'         => \App\Enums\DinnerBookingStatus::class,
    ];

    /**
     * Relazione con la disponibilità dell'host.
     *
     * Ogni prenotazione è associata a una specifica disponibilità
     * dichiarata dall'host per una data cena.
     *
     * @return BelongsTo Relazione con il modello DinnerAvailability
     */
    public function hostAvailability(): BelongsTo
    {
        return $this->belongsTo(DinnerAvailability::class, 'host_availability_id');
    }

    /**
     * Relazione con l'utente che prenota (guest).
     *
     * Identifica il membro del gruppo che effettua la prenotazione
     * come ospite presso un altro membro.
     *
     * @return BelongsTo Relazione con il modello User
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    /**
     * Calcola il totale degli ospiti (guest + accompagnatori).
     *
     * Restituisce il numero totale di persone che parteciperanno
     * alla cena, includendo il guest principale e gli accompagnatori.
     *
     * @return int Numero totale di ospiti
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->guests_count; // +1 per il guest stesso
    }

    /**
     * Scope per ottenere solo le prenotazioni confermate.
     *
     * Filtra la query per includere solo le prenotazioni con
     * status 'confirmed'.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Query builder
     * @return \Illuminate\Database\Eloquent\Builder Query filtrata
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope per ottenere solo le prenotazioni non confermate.
     *
     * Filtra la query per escludere le prenotazioni con
     * status 'confirmed', includendo quelle in attesa o cancellate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Query builder
     * @return \Illuminate\Database\Eloquent\Builder Query filtrata
     */
    public function scopeNotConfirmed($query)
    {
        return $query->whereNot('status', 'confirmed');
    }

    /**
     * Scope per ottenere solo le prenotazioni cancellate.
     *
     * Filtra la query per includere solo le prenotazioni con
     * status 'cancelled'.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Query builder
     * @return \Illuminate\Database\Eloquent\Builder Query filtrata
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Accessor per verificare se la prenotazione può essere modificata.
     *
     * Una prenotazione può essere modificata solo se lo stato della
     * disponibilità dell'host permette aggiornamenti (es. non è completata
     * o cancellata).
     *
     * @return Attribute Attributo computed che ritorna bool
     */
    public function canBeModified(): Attribute
    {
        return new Attribute(
            get: fn () => $this->hostAvailability->status->canUpdateBookings()
        );
    }
}
