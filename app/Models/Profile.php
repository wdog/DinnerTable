<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'privacy_accepted_at' => 'datetime',
            'max_guests' => 'integer',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the profile is complete.
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
}
