<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model per il logging degli eventi (disponibilità e prenotazioni).
 *
 * Traccia cronologicamente tutte le azioni e cambamenti di stato
 * relativi alle disponibilità e prenotazioni, creando un audit trail persistente.
 *
 * Caratteristiche:
 * - Record immutabili (solo created_at, no updated_at)
 * - Polymorphic relation per estendibilità a DinnerBooking
 * - logged_by nullable per eventi di sistema (cron job)
 * - Helper statico logEvent() per creazione facile
 * - Scopes per query comuni
 *
 * @property int $id
 * @property int|null $logged_by NULLABLE - null per eventi di sistema (cron job)
 * @property string $loggable_type
 * @property int $loggable_id
 * @property int $availability_id
 * @property string $status Status dopo l'evento
 * @property array|null $metadata Dati aggiuntivi JSON
 * @property \Carbon\Carbon $created_at
 */
class DinnerLog extends Model
{
    /**
     * Disabilita updated_at timestamp (record immutabili).
     */
    const UPDATED_AT = null;

    /**
     * Nome tabella.
     */
    protected $table = 'dinner_logs';

    /**
     * Attributi mass assignable.
     */
    protected $fillable = [
        'logged_by',
        'loggable_type',
        'loggable_id',
        'availability_id',
        'status',
        'metadata',
    ];

    /**
     * Cast degli attributi.
     */
    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Helper per creare log entry per DinnerAvailability.
     *
     * Metodo statico per creare facilmente un log entry con tutti i dati necessari.
     * Gestisce automaticamente la relazione polymorphic e availability_id.
     *
     * @param  DinnerAvailability  $availability  Availability associata all'evento
     * @param  string  $status  Status DOPO l'evento (enum value)
     * @param  int|null  $userId  NULLABLE - passa null per eventi di sistema (cron)
     * @param  array|null  $metadata  Dati aggiuntivi opzionali (event, old_status, etc)
     */
    public static function logEvent(
        DinnerAvailability $availability,
        string $status,
        ?int $userId = null,
        ?array $metadata = null
    ): ?self {
        return $availability->can_host ?
            self::create([
                'logged_by'       => $userId, // NULLABLE per eventi di sistema
                'loggable_type'   => DinnerAvailability::class,
                'loggable_id'     => $availability->id,
                'availability_id' => $availability->id,
                'status'          => $status,
                'metadata'        => $metadata,
            ])
            : null;
    }

    /**
     * Helper per creare log entry per DinnerBooking.
     *
     * Crea un log entry per eventi di prenotazione (creazione, cambio stato, modifiche).
     * Popola automaticamente availability_id dalla prenotazione per query facili.
     *
     * @param  DinnerBooking  $booking  Booking associato all'evento
     * @param  string  $status  Status DOPO l'evento (enum value)
     * @param  int|null  $userId  NULLABLE - utente che ha causato l'evento
     * @param  array|null  $metadata  Dati aggiuntivi (event, old_status, guests_count, etc)
     * @return self Log entry creato
     */
    public static function logBookingEvent(
        DinnerBooking $booking,
        string $status,
        ?int $userId = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'logged_by'       => $userId,
            'loggable_type'   => DinnerBooking::class,
            'loggable_id'     => $booking->id,
            'availability_id' => $booking->host_availability_id,
            'status'          => $status,
            'metadata'        => $metadata,
        ]);
    }

    /**
     * Utente che ha causato l'evento (nullable per eventi di sistema).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    /**
     * Entità loggabile (polymorphic).
     *
     * Può essere DinnerAvailability o DinnerBooking (Part 2).
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Availability associata.
     *
     * Sempre presente per query facili, anche quando loggable è un booking.
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(DinnerAvailability::class);
    }

    /**
     * Scope per filtrare per availability.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAvailability($query, int $availabilityId)
    {
        return $query->where('availability_id', $availabilityId);
    }

    /**
     * Scope per ordinamento cronologico (più recenti prima).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChronological($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
