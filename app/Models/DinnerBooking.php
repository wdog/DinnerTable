<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\DinnerBookingObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

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
    ];

    /**
     * Relazione con la disponibilità dell'host.
     */
    public function hostAvailability(): BelongsTo
    {
        return $this->belongsTo(DinnerAvailability::class, 'host_availability_id');
    }

    /**
     * Relazione con l'utente che prenota (guest).
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    /**
     * Calcola il totale degli ospiti (guest + accompagnatori).
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->guests_count; // +1 per il guest stesso
    }

    /**
     * Scope per ottenere solo le prenotazioni confermate.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope per ottenere solo le prenotazioni cancellate.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
