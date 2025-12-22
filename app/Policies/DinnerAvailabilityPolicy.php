<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DinnerAvailability;

/**
 * Policy per l'autorizzazione delle operazioni su DinnerAvailability.
 *
 * Gestisce i permessi per le disponibilità degli utenti a ospitare o
 * partecipare alle cene. Implementa regole business per garantire che:
 * - Gli utenti vedano solo le disponibilità del proprio gruppo
 * - Solo il proprietario possa modificare/eliminare le proprie disponibilità
 * - Non si possano eliminare disponibilità con prenotazioni confermate
 *
 * Regole principali:
 * - viewAny: tutti gli utenti autenticati possono vedere la lista
 * - view: solo disponibilità dello stesso gruppo
 * - create: tutti possono creare (filtro gruppo applicato a livello risorsa)
 * - update: solo il proprietario
 * - delete: solo proprietario E senza prenotazioni confermate
 * - restore/forceDelete: disabilitati (no soft delete)
 *
 * @see \App\Models\DinnerAvailability
 * @see \App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource
 */
class DinnerAvailabilityPolicy
{
    /**
     * Determina se l'utente può visualizzare la lista delle disponibilità.
     *
     * Tutti gli utenti autenticati possono accedere alla lista.
     * Il filtro per gruppo viene applicato a livello di query nella risorsa
     * Filament tramite getEloquentQuery().
     *
     * @param User $user Utente autenticato
     * @return bool True per tutti gli utenti autenticati
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina se l'utente può visualizzare una specifica disponibilità.
     *
     * L'utente può vedere solo le disponibilità che appartengono a utenti
     * del suo stesso gruppo dinner. Questo previene l'accesso a disponibilità
     * di altri gruppi tramite URL diretti.
     *
     * Controllo:
     * - Verifica che dinner_group_id dell'utente corrisponda al gruppo
     *   della data (dinnerDate) associata alla disponibilità
     *
     * @param User $user Utente autenticato
     * @param DinnerAvailability $dinnerAvailability Disponibilità da visualizzare
     * @return bool True se l'utente appartiene allo stesso gruppo
     */
    public function view(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return $user->dinner_group_id === $dinnerAvailability->dinnerDate->dinner_group_id;
    }

    /**
     * Determina se l'utente può creare nuove disponibilità.
     *
     * Tutti gli utenti autenticati possono dichiarare la propria disponibilità.
     * La validazione che l'utente appartenga a un gruppo viene gestita
     * a livello di form/risorsa Filament.
     *
     * @param User $user Utente autenticato
     * @return bool True per tutti gli utenti autenticati
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina se l'utente può modificare una disponibilità.
     *
     * Solo il proprietario della disponibilità può modificarla.
     * Questo previene che altri membri del gruppo modifichino
     * disponibilità altrui.
     *
     * Controllo:
     * - Verifica che user_id della disponibilità corrisponda all'utente autenticato
     *
     * @param User $user Utente autenticato
     * @param DinnerAvailability $dinnerAvailability Disponibilità da modificare
     * @return bool True se l'utente è il proprietario
     */
    public function update(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return $user->id === $dinnerAvailability->user_id;
    }

    /**
     * Determina se l'utente può eliminare una disponibilità.
     *
     * L'eliminazione è permessa solo se:
     * 1. L'utente è il proprietario della disponibilità
     * 2. Non ci sono prenotazioni confermate associate
     *
     * Questa regola protegge le prenotazioni dei guest: un host non può
     * eliminare una disponibilità se ci sono già persone che hanno prenotato
     * e sono state confermate. In questo caso, l'host deve prima cancellare
     * la disponibilità (cambiando stato a HOST_CANCELLED) che gestirà
     * automaticamente le notifiche ai guest.
     *
     * Flusso consigliato per cancellazione:
     * - Con prenotazioni confermate: cambia stato a HOST_CANCELLED
     * - Senza prenotazioni: può eliminare direttamente il record
     *
     * @param User $user Utente autenticato
     * @param DinnerAvailability $dinnerAvailability Disponibilità da eliminare
     * @return bool True se proprietario e senza prenotazioni confermate
     */
    public function delete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        // Può eliminare solo se è il proprietario
        if ($user->id !== $dinnerAvailability->user_id) {
            return false;
        }

        // Non può eliminare se ci sono prenotazioni confermate
        $hasConfirmedBookings = $dinnerAvailability->bookings()
            ->where('status', 'confirmed')
            ->exists();

        return ! $hasConfirmedBookings;
    }

    /**
     * Determina se l'utente può ripristinare una disponibilità eliminata.
     *
     * Funzionalità disabilitata: le disponibilità non usano soft delete,
     * quindi non c'è possibilità di ripristino.
     *
     * @param User $user Utente autenticato
     * @param DinnerAvailability $dinnerAvailability Disponibilità da ripristinare
     * @return bool Sempre false (funzionalità disabilitata)
     */
    public function restore(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return false;
    }

    /**
     * Determina se l'utente può eliminare permanentemente una disponibilità.
     *
     * Funzionalità disabilitata: le disponibilità non usano soft delete,
     * quindi non esiste eliminazione "permanente" separata da delete().
     *
     * @param User $user Utente autenticato
     * @param DinnerAvailability $dinnerAvailability Disponibilità da eliminare
     * @return bool Sempre false (funzionalità disabilitata)
     */
    public function forceDelete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return false;
    }
}
