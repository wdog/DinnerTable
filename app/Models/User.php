<?php

namespace App\Models;

use Filament\Panel;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasFactory,  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'dinner_group_id',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_admin' => false,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Solo gli admin possono accedere al pannello admin
        if ($panel->getId() === 'admin') {
            return $this->is_admin === true;
        }

        // Tutti gli utenti verificati possono accedere al pannello friend
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile?->avatar_url
            ? asset('storage/'.$this->profile->avatar_url)
            : null;
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the dinner group that the user belongs to.
     */
    public function dinnerGroup(): BelongsTo
    {
        return $this->belongsTo(DinnerGroup::class);
    }

    /**
     * Check if user has completed their profile.
     */
    public function hasCompletedProfile(): bool
    {
        return $this->profile && $this->profile->isComplete();
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Automatically create an empty profile for new users
            $user->profile()->create();
        });
    }

    public function dates(): HasMany
    {
        return $this->hasMany(DinnerDate::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(DinnerAvailability::class);
    }

    /**
     * Prenotazioni effettuate dall'utente come guest.
     */
    public function guestBookings(): HasMany
    {
        return $this->hasMany(DinnerBooking::class, 'guest_user_id');
    }
}
