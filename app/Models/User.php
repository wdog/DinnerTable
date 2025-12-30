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
     * Bootstrap del modello e dei suoi trait.
     *
     * Configura i lifecycle hooks del modello. Quando viene creato un nuovo
     * utente, viene automaticamente creato anche un profilo vuoto associato.
     */
    protected static function booted(): void
    {
        static::created(function ($user) {
            // Automatically create an empty profile for new users
            $user->profile()->create();
        });
    }

    /**
     * Verifica se l'utente può accedere a un pannello Filament specifico.
     *
     * Determina i permessi di accesso in base al tipo di pannello:
     * - Pannello 'admin': riservato solo agli amministratori (is_admin = true)
     * - Pannello 'app': accessibile a tutti gli utenti autenticati
     *
     * @param  Panel  $panel  Il pannello Filament da verificare
     * @return bool True se l'utente può accedere al pannello, false altrimenti
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Solo gli admin possono accedere al pannello admin
        if ($panel->getId() === 'admin') {
            return $this->is_admin === true;
        }

        // Tutti gli utenti verificati possono accedere al pannello friend
        return true;
    }

    /**
     * Ottiene l'URL dell'avatar dell'utente per Filament.
     *
     * Restituisce l'URL completo dell'avatar se presente nel profilo,
     * altrimenti ritorna null. L'URL viene costruito utilizzando la
     * directory di storage pubblica di Laravel.
     *
     * @return string|null L'URL dell'avatar o null se non presente
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile?->avatar_url
            ? asset('storage/' . $this->profile->avatar_url)
            : null;
    }

    /**
     * Relazione uno-a-uno con il profilo utente.
     *
     * Ogni utente ha un profilo associato che contiene informazioni
     * aggiuntive come nome, cognome, indirizzo e foto.
     *
     * @return HasOne Relazione con il modello Profile
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relazione con il gruppo cena a cui l'utente appartiene.
     *
     * Ogni utente può appartenere a un solo gruppo cena alla volta.
     * Il gruppo coordina le disponibilità e le prenotazioni dei membri.
     *
     * @return BelongsTo Relazione con il modello DinnerGroup
     */
    public function dinnerGroup(): BelongsTo
    {
        return $this->belongsTo(DinnerGroup::class);
    }

    /**
     * Verifica se l'utente ha completato il proprio profilo.
     *
     * Controlla che esista un profilo associato e che tutti i campi
     * obbligatori siano stati compilati dall'utente.
     *
     * @return bool True se il profilo è completo, false altrimenti
     */
    public function hasCompletedProfile(): bool
    {
        return $this->profile && $this->profile->isComplete();
    }

    /**
     * Relazione con le date cena create dall'utente.
     *
     * Ottiene tutte le date per le quali l'utente ha dichiarato
     * una disponibilità (come host o come guest).
     *
     * @return HasMany Relazione con il modello DinnerDate
     */
    public function dates(): HasMany
    {
        return $this->hasMany(DinnerDate::class);
    }

    /**
     * Relazione con le disponibilità dichiarate dall'utente.
     *
     * Ottiene tutte le disponibilità (come host o guest) che
     * l'utente ha dichiarato per le varie date cena.
     *
     * @return HasMany Relazione con il modello DinnerAvailability
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(DinnerAvailability::class);
    }

    /**
     * Prenotazioni effettuate dall'utente come guest.
     *
     * Ottiene tutte le prenotazioni in cui l'utente partecipa
     * come ospite presso altri membri del gruppo.
     *
     * @return HasMany Relazione con il modello DinnerBooking
     */
    public function guestBookings(): HasMany
    {
        return $this->hasMany(DinnerBooking::class, 'guest_user_id');
    }

    /**
     * Relazione con AppReview.
     *
     * Ogni utente può lasciare una sola recensione dell'applicazione.
     */
    public function appReview(): HasOne
    {
        return $this->hasOne(AppReview::class);
    }

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
}
