<?php

namespace App\Models;

use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Database\Eloquent\Model;
use App\Observers\DinnerAvailabilityObserver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * Modello per le disponibilità degli utenti per le cene.
 *
 * Rappresenta la dichiarazione di disponibilità di un utente per una specifica
 * data cena. L'utente può dichiararsi disponibile come host (ospitante) o come
 * guest (partecipante).
 *
 * Funzionalità principali:
 * - Gestione dello stato della disponibilità (disponibile, confermata, completata, cancellata)
 * - Validazione automatica tra can_host e status appropriato
 * - Calcolo automatico dei posti disponibili per gli host
 * - Relazioni con prenotazioni e utenti
 *
 * @see DinnerAvailabilityObserver Observer per logica automatica
 * @see DinnerAvailabilityStatus Enum per gli stati possibili
 * @see DinnerBooking Modello per le prenotazioni associate
 */
#[ObservedBy([DinnerAvailabilityObserver::class])]
class DinnerAvailability extends Model
{
    protected $fillable = [
        'dinner_date_id',
        'user_id',
        'status',
        'can_host',
        'dinner_name',
        'max_guests',
        'note',
        'cancellation_reason',
    ];

    protected $casts = [
        'status'              => DinnerAvailabilityStatus::class,
        'can_host'            => 'boolean',
        'max_guests'          => 'integer',
        'cancellation_reason' => \App\Enums\CancellationReason::class,
    ];

    /**
     * Bootstrap del modello per la configurazione dei lifecycle hooks.
     *
     * Configura le validazioni automatiche eseguite prima del salvataggio:
     * - Sincronizza lo stato (status) con il flag can_host
     * - Rimuove max_guests se l'utente non è host
     * - Previene inconsistenze tra ruolo (host/guest) e stato
     */
    protected static function booted(): void
    {
        static::saving(function (self $model) {
            // Validazione: se can_host è false, lo stato deve essere un guest status
            if ($model->can_host === false && $model->status->isHostStatus()) {
                $model->status = DinnerAvailabilityStatus::AVAILABLE;
            }

            // Validazione: se can_host è true, lo stato deve essere un host status
            if ($model->can_host === true && $model->status->isGuestStatus()) {
                $model->status = DinnerAvailabilityStatus::AVAILABLE_TO_HOST;
            }

            // Se can_host è false, max_guests e dinner_name devono essere null
            if ($model->can_host === false) {
                $model->max_guests  = null;
                $model->dinner_name = null;
            }
        });
    }

    /**
     * Relazione con la data cena associata.
     *
     * Ogni disponibilità è legata a una specifica data cena del gruppo.
     *
     * @return BelongsTo Relazione con il modello DinnerDate
     */
    public function dinnerDate(): BelongsTo
    {
        return $this->belongsTo(DinnerDate::class, 'dinner_date_id');
    }

    /**
     * Relazione con l'utente che ha dichiarato la disponibilità.
     *
     * Identifica quale membro del gruppo ha creato questa disponibilità.
     *
     * @return BelongsTo Relazione con il modello User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione con le prenotazioni ricevute (quando questo utente è host).
     *
     * Se l'utente si è dichiarato disponibile come host, questa relazione
     * contiene tutte le prenotazioni effettuate dagli altri membri del gruppo.
     *
     * @return HasMany Relazione con il modello DinnerBooking
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(DinnerBooking::class, 'host_availability_id');
    }

    /**
     * Logs cronologici per questa disponibilità.
     *
     * Relazione diretta con i log tramite availability_id per query facili.
     * I log sono ordinati cronologicamente (dal più vecchio al più nuovo).
     *
     * @return HasMany Relazione con il modello DinnerLog
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DinnerLog::class, 'availability_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Polymorphic relation per activity logs.
     *
     * Relazione polymorphic per estendibilità futura a bookings.
     * Preferire logs() per query semplici su availability.
     *
     * @return MorphMany Relazione polymorphic con DinnerLog
     */
    public function activityLogs(): MorphMany
    {
        return $this->morphMany(DinnerLog::class, 'loggable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Ottiene solo le prenotazioni confermate.
     *
     * Filtra le prenotazioni per ottenere solo quelle con status 'confirmed',
     * escludendo quelle in attesa o cancellate.
     *
     * @return HasMany Relazione filtrata con prenotazioni confermate
     */
    public function confirmedBookings(): HasMany
    {
        return $this->bookings()->where('status', 'confirmed');
    }

    /**
     * Calcola il totale degli ospiti già prenotati (confermati).
     *
     * Somma il numero di ospiti di tutte le prenotazioni confermate
     * per questa disponibilità. Considera solo le prenotazioni con
     * status 'confirmed'.
     *
     * @return int Numero totale di ospiti prenotati
     */
    public function getTotalBookedGuestsAttribute(): int
    {
        return $this->confirmedBookings()
            ->get()
            ->sum(function ($booking) {
                return $booking->total_guests;
            });
    }

    /**
     * Verifica se ci sono ancora posti disponibili.
     *
     * Controlla se l'host può ancora accettare nuove prenotazioni
     * confrontando i posti occupati con il massimo consentito.
     *
     * @return bool True se ci sono posti disponibili, false altrimenti
     */
    public function hasAvailableSpots(): bool
    {
        if ( ! $this->can_host || ! $this->max_guests) {
            return false;
        }

        return $this->total_booked_guests < $this->max_guests;
    }

    /**
     * Ottiene il numero di posti ancora disponibili.
     *
     * Calcola la differenza tra il numero massimo di ospiti consentiti
     * e quelli già prenotati. Ritorna 0 se non è un host o se non ci
     * sono posti liberi.
     *
     * @return int Numero di posti disponibili
     */
    public function getAvailableSpotsAttribute(): int
    {
        if ( ! $this->can_host || ! $this->max_guests) {
            return 0;
        }

        return max(0, $this->max_guests - $this->total_booked_guests);
    }

    /**
     * Verifica se l'host può accettare nuove prenotazioni.
     *
     * Controlla tutte le condizioni necessarie per accettare prenotazioni:
     * - Deve essere dichiarato come host (can_host = true)
     * - Lo stato deve permettere prenotazioni (es. AVAILABLE_TO_HOST)
     * - Devono esserci posti disponibili
     *
     * @return bool True se può accettare prenotazioni, false altrimenti
     */
    public function canAcceptBookings(): bool
    {
        return $this->can_host
            && $this->status->canAcceptBookings()
            && $this->hasAvailableSpots();
    }

    /**
     * Scope per filtrare solo le disponibilità future.
     *
     * Include solo le disponibilità la cui data cena è nel futuro.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFuture($query)
    {
        return $query->whereHas('dinnerDate', function ($query) {
            $query->where('dinner_date', '>=', now()->startOfDay());
        });
    }

    /**
     * Scope per filtrare solo le disponibilità passate.
     *
     * Include solo le disponibilità la cui data cena è nel passato.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePast($query)
    {
        return $query->whereHas('dinnerDate', function ($query) {
            $query->where('dinner_date', '<', now()->startOfDay());
        });
    }
}
