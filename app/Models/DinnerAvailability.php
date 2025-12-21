<?php

namespace App\Models;

use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DinnerAvailability extends Model
{
    //
    protected $fillable = [
        'dinner_date_id',
        'user_id',
        'status',
        'can_host',
        'max_guests',
        'note',
    ];

    protected $casts = [
        'status' => DinnerAvailabilityStatus::class,
        'can_host' => 'boolean',
        'max_guests' => 'integer',
    ];

    protected static function booted()
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

            // Se can_host è false, max_guests deve essere null
            if ($model->can_host === false) {
                $model->max_guests = null;
            }
        });
    }

    public function dinnerDate(): BelongsTo
    {
        return $this->belongsTo(DinnerDate::class, 'dinner_date_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione con le prenotazioni ricevute (quando questo utente è host).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(DinnerBooking::class, 'host_availability_id');
    }

    /**
     * Ottiene solo le prenotazioni confermate.
     */
    public function confirmedBookings(): HasMany
    {
        return $this->bookings()->where('status', 'confirmed');
    }

    /**
     * Calcola il totale degli ospiti già prenotati (confermati).
     */
    public function getTotalBookedGuestsAttribute(): int
    {
        return $this->confirmedBookings()
            ->get()
            ->sum(function ($booking) {
                return $booking->total_guests; // guests_count + 1 (il guest stesso)
            });
    }

    /**
     * Verifica se ci sono ancora posti disponibili.
     */
    public function hasAvailableSpots(): bool
    {
        if (! $this->can_host || ! $this->max_guests) {
            return false;
        }

        return $this->total_booked_guests < $this->max_guests;
    }

    /**
     * Ottiene il numero di posti ancora disponibili.
     */
    public function getAvailableSpotsAttribute(): int
    {
        if (! $this->can_host || ! $this->max_guests) {
            return 0;
        }

        return max(0, $this->max_guests - $this->total_booked_guests);
    }

    /**
     * Verifica se l'host può accettare nuove prenotazioni.
     */
    public function canAcceptBookings(): bool
    {
        return $this->can_host
            && $this->status->canAcceptBookings()
            && $this->hasAvailableSpots();
    }
}
