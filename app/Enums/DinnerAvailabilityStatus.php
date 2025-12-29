<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum per gli stati delle disponibilità degli utenti (DinnerAvailability).
 *
 * Questo enum gestisce due flussi distinti:
 *
 * 1. **Stati HOST** (can_host = true):
 *    - L'utente si dichiara disponibile ad ospitare una cena
 *    - Gli stati evolvono in base al numero di prenotazioni ricevute
 *    - Gestisce la capacità massima di ospiti (max_guests)
 *
 * 2. **Stati GUEST** (can_host = false):
 *    - L'utente dichiara la propria disponibilità a partecipare come ospite
 *    - AVAILABLE: disponibile a essere ospitato
 *    - NOT_AVAILABLE: non disponibile per quella data (comunica assenza al gruppo)
 *
 * Ciclo di vita per HOST:
 * - AVAILABLE_TO_HOST: appena creata, può ricevere prenotazioni
 * - ALMOST_FULL: vicino al limite massimo di ospiti
 * - FULL: raggiunto il numero massimo di ospiti
 * - HOST_CANCELLED: host ha cancellato la disponibilità
 * - COMPLETED: cena conclusa (impostato automaticamente da cron job)
 *
 * @see \App\Models\DinnerAvailability
 * @see \App\Observers\DinnerAvailabilityObserver
 * @see \App\Console\Commands\CompleteExpiredAvailabilities
 */
enum DinnerAvailabilityStatus: string implements HasColor, HasIcon, HasLabel
{
    /**
     * Host disponibile ad ospitare con posti liberi.
     *
     * Stato iniziale per disponibilità host. Indica che l'utente
     * è disponibile ad ospitare e può ancora accettare prenotazioni.
     */
    case AVAILABLE_TO_HOST = 'available_to_host';

    /**
     * Host quasi al completo.
     *
     * Indica che il numero di prenotazioni si sta avvicinando
     * al limite massimo (max_guests). Può ancora accettare prenotazioni.
     */
    case ALMOST_FULL = 'almost_full';

    /**
     * Host al completo, non può accettare altre prenotazioni.
     *
     * Raggiunto il numero massimo di ospiti. Non accetta nuove prenotazioni.
     */
    case FULL = 'full';

    /**
     * Disponibilità cancellata dall'host.
     *
     * L'host ha annullato la disponibilità. Tutte le prenotazioni
     * associate vengono automaticamente cancellate.
     */
    case HOST_CANCELLED = 'host_cancelled';

    /**
     * Cena completata (evento passato).
     *
     * Impostato automaticamente dal comando schedulato quando
     * la data della cena è passata. Stato finale, non modificabile.
     */
    case COMPLETED = 'completed';

    /**
     * Guest disponibile a partecipare.
     *
     * Stato per disponibilità guest (can_host = false).
     * Indica che l'utente è disponibile a essere ospitato.
     */
    case AVAILABLE = 'available';

    /**
     * Guest non disponibile a partecipare.
     *
     * Stato per disponibilità guest (can_host = false).
     * Indica che l'utente ha dichiarato di non essere disponibile
     * per quella data specifica (es. impegni, vacanza, ecc.).
     * Serve per comunicare al gruppo la propria assenza.
     */
    case NOT_AVAILABLE = 'not_available';

    /**
     * Restituisce l'etichetta tradotta dello stato.
     *
     * Implementazione dell'interfaccia HasLabel di Filament.
     * Fornisce le etichette italiane per ogni stato da mostrare nell'UI.
     *
     * @return string Label tradotta in italiano
     */
    public function getLabel(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'Disponibile ad ospitare',
            self::ALMOST_FULL       => 'Quasi pieno',
            self::FULL              => 'Pieno',
            self::HOST_CANCELLED    => 'Annullato',
            self::COMPLETED         => 'Completato',
            // Guest state
            self::AVAILABLE     => 'Disponibile',
            self::NOT_AVAILABLE => 'Non piu\' disponibile',
        };
    }

    /**
     * Restituisce il colore associato allo stato.
     *
     * Implementazione dell'interfaccia HasColor di Filament.
     * I colori seguono la semantica Filament:
     * - success (verde): disponibilità attiva
     * - warning (giallo): attenzione richiesta
     * - danger (rosso): stato critico o negativo
     * - info (blu): stato informativo
     *
     * @return string Nome del colore Filament
     */
    public function getColor(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'success',  // Verde: tutto ok
            self::ALMOST_FULL       => 'warning',  // Giallo: attenzione, quasi pieno
            self::FULL              => 'danger',   // Rosso: pieno
            self::HOST_CANCELLED    => 'danger',   // Rosso: cancellato
            self::COMPLETED         => 'info',     // Blu: completato
            // Guest states
            self::AVAILABLE     => 'warning',      // Verde: disponibile
            self::NOT_AVAILABLE => 'gray',         // Grigio: non disponibile
        };
    }

    /**
     * Restituisce l'icona associata allo stato.
     *
     * Implementazione dell'interfaccia HasIcon di Filament.
     * Utilizza icone Tabler per rappresentare visivamente ogni stato.
     *
     * @return string Nome dell'icona Tabler
     */
    public function getIcon(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'tabler-home-2',  // Cappello da chef
            self::ALMOST_FULL       => 'tabler-users',            // Gruppo utenti
            self::FULL              => 'tabler-door-off',         // Porta chiusa
            self::HOST_CANCELLED    => 'tabler-ban',              // Simbolo divieto
            self::COMPLETED         => 'tabler-thumb-up',         // Pollice su
            // Guest states
            self::AVAILABLE     => 'tabler-tools-kitchen-3',      // Utensili cucina
            self::NOT_AVAILABLE => 'tabler-calendar-x',           // Calendario con X
        };
    }

    /**
     * Verifica se lo stato è valido per un host.
     *
     * Controlla se lo stato corrente appartiene al flusso HOST,
     * ovvero se la disponibilità è per ospitare (can_host = true).
     *
     * Stati HOST: AVAILABLE_TO_HOST, ALMOST_FULL, FULL,
     *             HOST_CANCELLED, COMPLETED
     *
     * @return bool True se è uno stato per host
     */
    public function isHostStatus(): bool
    {
        return in_array($this, [
            self::AVAILABLE_TO_HOST,
            self::ALMOST_FULL,
            self::FULL,
            self::HOST_CANCELLED,
            self::COMPLETED,
        ]);
    }

    /**
     * Verifica se lo stato è valido per un guest.
     *
     * Controlla se lo stato corrente appartiene al flusso GUEST,
     * ovvero se la disponibilità è per partecipare (can_host = false).
     *
     * Stati GUEST: AVAILABLE, NOT_AVAILABLE
     *
     * @return bool True se è uno stato per guest
     */
    public function isGuestStatus(): bool
    {
        return in_array($this, [
            self::AVAILABLE,
            self::NOT_AVAILABLE,
        ]);
    }

    /**
     * Verifica se l'host può accettare nuove prenotazioni.
     *
     * Determina se una disponibilità host può ancora ricevere
     * nuove prenotazioni in base allo stato corrente.
     *
     * Stati che accettano prenotazioni:
     * - AVAILABLE_TO_HOST: ancora posti disponibili
     * - ALMOST_FULL: quasi pieno ma accetta ancora
     *
     * Stati che NON accettano prenotazioni:
     * - FULL: raggiunto il massimo
     * - HOST_CANCELLED: cancellato dall'host
     * - COMPLETED: cena già avvenuta
     *
     * Utilizzato da:
     * - DinnerBookingPolicy per validare nuove prenotazioni
     * - UI per mostrare/nascondere pulsante "Prenota"
     *
     * @return bool True se può accettare prenotazioni
     *
     * @see \App\Policies\DinnerBookingPolicy::book()
     */
    public function canAcceptBookings(): bool
    {
        return in_array($this, [
            self::AVAILABLE_TO_HOST,
            self::ALMOST_FULL,
        ]);
    }

    /**
     * Verifica se le prenotazioni associate possono essere modificate.
     *
     * Determina se le prenotazioni (DinnerBooking) collegate a questa
     * disponibilità possono ancora essere modificate o cancellate dai guest.
     *
     * Stati che permettono modifiche:
     * - AVAILABLE_TO_HOST: prenotazioni modificabili
     * - ALMOST_FULL: prenotazioni modificabili
     * - FULL: prenotazioni modificabili
     *
     * Stati che NON permettono modifiche:
     * - COMPLETED: cena conclusa, non più modificabile
     * - HOST_CANCELLED: host ha cancellato, prenotazioni annullate
     *
     * Utilizzato da:
     * - DinnerBooking::canBeModified() per controllo accesso
     * - Form prenotazioni per disabilitare campi
     *
     * @return bool True se le prenotazioni possono essere modificate
     *
     * @see \App\Models\DinnerBooking::canBeModified()
     */
    public function canUpdateBookings(): bool
    {
        return ! in_array($this, [
            self::COMPLETED,
            self::HOST_CANCELLED,
        ]);
    }
}
