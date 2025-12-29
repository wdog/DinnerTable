<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

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
     * @param  User  $user  Utente autenticato
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
     * @param  User  $user  Utente autenticato
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità da visualizzare
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
     * @param  User  $user  Utente autenticato
     * @return bool True per tutti gli utenti autenticati
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina se l'utente può modificare una disponibilità.
     *
     * La modifica è permessa solo se:
     * 1. L'utente è il proprietario della disponibilità
     * 2. Lo stato NON è COMPLETED (cena già conclusa)
     *
     * Questo previene:
     * - Altri membri del gruppo modifichino disponibilità altrui
     * - Modifiche a cene già concluse (storico immutabile)
     *
     * Stati modificabili:
     * - AVAILABLE_TO_HOST, ALMOST_FULL, FULL (stati attivi host)
     * - HOST_CANCELLED (può essere riattivato cambiando stato)
     * - AVAILABLE, BOOKED, UNAVAILABLE (stati guest)
     *
     * Stati NON modificabili:
     * - COMPLETED: cena già avvenuta, dato storico
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità da modificare
     * @return bool True se proprietario e non completato
     */
    public function update(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        // Deve essere il proprietario
        if ($user->id !== $dinnerAvailability->user_id) {
            return false;
        }

        // Non può modificare disponibilità completate (cena conclusa)
        if ($dinnerAvailability->status === DinnerAvailabilityStatus::COMPLETED) {
            return false;
        }

        return true;
    }

    /**
     * Determina se l'utente può eliminare una disponibilità.
     *
     * L'eliminazione fisica (hard delete) è permessa solo se:
     * 1. L'utente è il proprietario della disponibilità
     * 2. Lo stato NON è COMPLETED (cena già conclusa, dato storico)
     * 3. Non ci sono prenotazioni associate (di qualsiasi stato)
     *
     * Questa regola protegge lo storico: un host non può eliminare una
     * disponibilità se ci sono prenotazioni collegate o se la cena è
     * già stata completata. Questo mantiene l'integrità dei dati storici.
     *
     * Flusso per cancellazione:
     * - **Senza prenotazioni E non completata**: può eliminare direttamente (hard delete)
     * - **Con prenotazioni** (pending/confirmed/cancelled): deve cambiare stato a HOST_CANCELLED
     * - **COMPLETED**: NON può eliminare né modificare (dato storico immutabile)
     *
     * Il cambio stato a HOST_CANCELLED:
     * - Mantiene le prenotazioni nel database per storico
     * - Impedisce nuove prenotazioni
     * - Notifica i guest della cancellazione
     * - Non elimina i dati ma li marca come cancellati
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità da eliminare
     * @return bool True se proprietario, non completata e senza prenotazioni
     */
    public function delete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        // Può eliminare solo se è il proprietario
        if ($user->id !== $dinnerAvailability->user_id) {
            return false;
        }

        // Non può eliminare disponibilità completate (dato storico)
        if ($dinnerAvailability->status === DinnerAvailabilityStatus::COMPLETED) {
            return false;
        }

        // Non può eliminare se ci sono prenotazioni di QUALSIASI stato
        // (pending, confirmed, cancelled) per mantenere lo storico
        $hasAnyBookings = $dinnerAvailability->bookings()->exists();

        return ! $hasAnyBookings;
    }

    /**
     * Determina se l'utente può ripristinare una disponibilità eliminata.
     *
     * Funzionalità disabilitata: le disponibilità non usano soft delete,
     * quindi non c'è possibilità di ripristino.
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità da ripristinare
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
     * @param  User  $user  Utente autenticato
     * @param  DinnerAvailability  $dinnerAvailability  Disponibilità da eliminare
     * @return bool Sempre false (funzionalità disabilitata)
     */
    public function forceDelete(User $user, DinnerAvailability $dinnerAvailability): bool
    {
        return false;
    }
}
