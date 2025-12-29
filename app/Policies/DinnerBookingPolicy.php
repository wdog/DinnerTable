<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DinnerBooking;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

/**
 * Policy per l'autorizzazione delle operazioni su DinnerBooking.
 *
 * Gestisce i permessi complessi per le prenotazioni delle cene, implementando
 * regole business che garantiscono:
 * - Un utente non può prenotare la propria disponibilità
 * - Un utente può avere solo una prenotazione confermata per giorno
 * - Solo membri dello stesso gruppo possono prenotare
 * - Rispetto dei limiti di posti disponibili
 * - Controllo stati disponibilità (canAcceptBookings)
 *
 * Ruoli coinvolti:
 * - GUEST: utente che prenota per partecipare a una cena
 * - HOST: utente che ospita e gestisce le prenotazioni
 *
 * @see \App\Models\DinnerBooking
 * @see \App\Models\DinnerAvailability
 * @see \App\Enums\DinnerAvailabilityStatus
 */
class DinnerBookingPolicy
{
    /**
     * Determina se l'utente può visualizzare la lista delle prenotazioni.
     *
     * Solo utenti che appartengono a un gruppo dinner possono vedere
     * le prenotazioni. Utenti senza gruppo non hanno accesso.
     *
     * @param  User  $user  Utente autenticato
     * @return bool True se l'utente appartiene a un gruppo
     */
    public function viewAny(User $user): bool
    {
        return $user->dinner_group_id !== null;
    }

    /**
     * Determina se l'utente può visualizzare una specifica prenotazione.
     *
     * L'utente può vedere una prenotazione se è coinvolto come:
     * - GUEST: ha effettuato la prenotazione
     * - HOST: ospita la cena per cui è stata fatta la prenotazione
     *
     * Questo permette a entrambe le parti di vedere i dettagli della prenotazione
     * mantenendo la privacy per utenti non coinvolti.
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerBooking  $booking  Prenotazione da visualizzare
     * @return bool True se è guest o host della prenotazione
     */
    public function view(User $user, DinnerBooking $booking): bool
    {
        // Può vedere se è il guest o l'host della disponibilità
        return $booking->guest_user_id === $user->id
            || $booking->hostAvailability->user_id === $user->id;
    }

    /**
     * Determina se l'utente può creare prenotazioni.
     *
     * Prerequisito base: l'utente deve appartenere a un gruppo dinner.
     * La validazione specifica per ogni disponibilità viene gestita
     * dal metodo book().
     *
     * @param  User  $user  Utente autenticato
     * @return bool True se l'utente appartiene a un gruppo
     */
    public function create(User $user): bool
    {
        return $user->dinner_group_id !== null;
    }

    /**
     * Determina se l'utente può prenotare una specifica disponibilità.
     *
     * Questo metodo implementa tutte le regole business per validare
     * una nuova prenotazione. Controlla 8 condizioni che devono essere
     * tutte soddisfatte:
     *
     * 1. L'utente deve appartenere a un gruppo
     * 2. L'utente non può prenotare la propria disponibilità (non può essere host e guest)
     * 3. L'utente deve essere dello stesso gruppo della disponibilità
     * 4. La disponibilità deve essere di tipo HOST (can_host = true)
     * 5. Lo stato deve permettere prenotazioni (AVAILABLE_TO_HOST o ALMOST_FULL)
     * 6. Devono esserci posti disponibili (guests_count < max_guests)
     * 7. L'utente non deve aver già prenotato questa disponibilità
     * 8. L'utente non deve avere altre prenotazioni confermate nello stesso giorno
     *
     * Regola #8 (una prenotazione per giorno):
     * Un utente può essere ospitato solo in una cena per giorno.
     * Questo previene prenotazioni multiple nello stesso giorno.
     *
     * Utilizzato da:
     * - Action "Prenota" nella pagina calendario gruppo
     * - Form creazione nuova prenotazione
     * - Validazione lato server
     *
     * @param  User  $user  Utente che vuole prenotare (guest)
     * @param  DinnerAvailability  $availability  Disponibilità da prenotare
     * @return bool True se tutte le condizioni sono soddisfatte
     *
     * @see \App\Enums\DinnerAvailabilityStatus::canAcceptBookings()
     * @see \App\Models\DinnerAvailability::hasAvailableSpots()
     */
    public function book(User $user, DinnerAvailability $availability): bool
    {
        // 1. L'utente deve appartenere a un gruppo
        if ($user->dinner_group_id === null) {
            return false;
        }

        // 2. Non può prenotare la propria disponibilità (non può essere host e guest insieme)
        if ($availability->user_id === $user->id) {
            return false;
        }

        // 3. Deve essere dello stesso gruppo della disponibilità
        if ($availability->dinnerDate->dinner_group_id !== $user->dinner_group_id) {
            return false;
        }

        // 4. La disponibilità deve essere di tipo host (can_host = true)
        if ( ! $availability->can_host) {
            return false;
        }

        // 5. Lo stato deve permettere prenotazioni (AVAILABLE_TO_HOST o ALMOST_FULL)
        if ( ! $availability->status->canAcceptBookings()) {
            return false;
        }

        // 6. Devono esserci posti disponibili (verifica tramite metodo hasAvailableSpots)
        if ( ! $availability->hasAvailableSpots()) {
            return false;
        }

        // 7. Non deve aver già prenotato questa disponibilità (prenotazione duplicata)
        // Include sia prenotazioni confermate che cancellate
        $hasAlreadyBooked = DinnerBooking::where('host_availability_id', $availability->id)
            ->where('guest_user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending', 'cancelled'])
            ->exists();

        if ($hasAlreadyBooked) {
            return false;
        }

        // 8. Non deve avere altre prenotazioni nello stesso giorno
        // Regola: un utente può partecipare solo a una cena per giorno
        // Include confirmed, pending e cancelled per evitare confusione
        $dinnerDate                = $availability->dinnerDate->dinner_date;
        $hasOtherBookingsOnSameDay = DinnerBooking::where('guest_user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending', 'cancelled'])
            ->whereHas('hostAvailability.dinnerDate', function ($query) use ($dinnerDate) {
                $query->where('dinner_date', $dinnerDate);
            })
            ->exists();

        if ($hasOtherBookingsOnSameDay) {
            return false;
        }

        return true;
    }

    /**
     * Determina se l'utente può modificare una prenotazione.
     *
     * Solo il guest (chi ha effettuato la prenotazione) può modificarla.
     * Può modificare:
     * - Numero di ospiti (guests_count)
     * - Stato (per cancellare la prenotazione)
     * - Note della prenotazione
     *
     * Restrizioni:
     * - Solo il GUEST può modificare
     * - Non può modificare prenotazioni CANCELLED (concluse)
     * - Non può modificare prenotazioni per disponibilità COMPLETED (cena conclusa)
     * - L'host non può modificare le prenotazioni dei guest, può solo
     *   visualizzarle
     *
     * La modificabilità è anche controllata dallo stato della disponibilità
     * tramite canUpdateBookings() nel form.
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerBooking  $booking  Prenotazione da modificare
     * @return bool True se guest, prenotazione attiva, e disponibilità non completata
     *
     * @see \App\Filament\App\Resources\DinnerBookings\Pages\EditDinnerBooking
     * @see \App\Enums\DinnerAvailabilityStatus::COMPLETED
     */
    public function update(User $user, DinnerBooking $booking): bool
    {
        // Solo il guest può modificare la propria prenotazione
        if ($booking->guest_user_id !== $user->id) {
            return false;
        }

        // Non può modificare prenotazioni per disponibilità completate (cena conclusa)
        if ($booking->hostAvailability->status === DinnerAvailabilityStatus::COMPLETED) {
            return false;
        }

        return true;
    }

    /**
     * Determina se l'host può confermare o rifiutare una prenotazione.
     *
     * Solo l'HOST (proprietario della disponibilità) può modificare lo status
     * delle prenotazioni ricevute (PENDING → CONFIRMED o CANCELLED).
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerBooking  $booking  Prenotazione da confermare/rifiutare
     * @return bool True se l'utente è l'host della disponibilità
     */
    public function updateGuestBooking(User $user, DinnerBooking $booking): bool
    {
        return $booking->hostAvailability->user_id === $user->id;
    }

    /**
     * Determina se l'utente può eliminare fisicamente una prenotazione.
     *
     * **IMPORTANTE**: Le prenotazioni NON possono essere eliminate fisicamente (hard delete).
     * Questo metodo restituisce sempre `false` per mantenere lo storico completo
     * di tutte le prenotazioni nel database.
     *
     * Perché bloccare l'eliminazione fisica:
     * - Mantiene lo storico completo per audit e statistiche
     * - Previene perdita di dati importanti
     * - Permette analisi future (chi ha prenotato, quando, cancellazioni, ecc.)
     * - Garantisce integrità referenziale con altre tabelle
     *
     * **Come cancellare una prenotazione**:
     * - Guest/Host devono cambiare lo stato a `CANCELLED` tramite update
     * - Questo è gestito tramite Action personalizzate o form di modifica
     * - NON usare il pulsante "Elimina" standard di Filament
     *
     * Flusso corretto per cancellazione:
     * 1. Guest/Host clicca "Cancella prenotazione"
     * 2. Action aggiorna `status` a CANCELLED (non elimina il record)
     * 3. Observer gestisce automaticamente:
     *    - Libera i posti per l'host
     *    - Aggiorna stato guest availability a AVAILABLE
     *    - Aggiorna stato host availability se necessario
     * 4. Prenotazione rimane nel database per storico
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerBooking  $booking  Prenotazione da eliminare
     * @return bool Sempre false - eliminazione fisica non permessa
     *
     * @see \App\Observers\DinnerBookingObserver
     * @see \App\Enums\DinnerBookingStatus
     */
    public function delete(User $user, DinnerBooking $booking): bool
    {
        // Le prenotazioni NON possono essere eliminate fisicamente
        // Solo il cambio stato a CANCELLED è permesso
        return false;
    }

    /**
     * Determina se l'utente può ripristinare una prenotazione soft-deleted.
     *
     * Solo il guest può ripristinare la propria prenotazione eliminata.
     * Funzionalità limitata, da valutare se mantenere nel flusso applicativo.
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerBooking  $booking  Prenotazione da ripristinare
     * @return bool True se l'utente è il guest della prenotazione
     */
    public function restore(User $user, DinnerBooking $booking): bool
    {
        return $booking->guest_user_id === $user->id;
    }

    /**
     * Determina se l'utente può eliminare definitivamente una prenotazione.
     *
     * Eliminazione permanente permessa solo a:
     * - Super admin: per gestione sistema
     * - Guest proprietario: per rimuovere definitivamente i propri dati
     *
     * L'eliminazione permanente rimuove completamente il record dal database
     * (non soft delete).
     *
     * @param  User  $user  Utente autenticato
     * @param  DinnerBooking  $booking  Prenotazione da eliminare
     * @return bool True se è super_admin o guest proprietario
     */
    public function forceDelete(User $user, DinnerBooking $booking): bool
    {
        // Solo admin o il guest possono eliminare definitivamente
        return $user->hasRole('super_admin') || $booking->guest_user_id === $user->id;
    }
}
