# Sistema Prenotazioni Cene - Stato Attuale

## Concetti Chiave

### Ruoli

**HOST (chi cucina/ospita)**:
- Crea `DinnerAvailability` con `can_host = true`
- Dichiara disponibilità ad ospitare per una data specifica
- Specifica numero massimo ospiti (`max_guests`)
- Stati disponibilità: `AVAILABLE_TO_HOST` → `ALMOST_FULL` → `FULL` → `HOST_CANCELLED` → `COMPLETED`

**GUEST (chi partecipa/mangia)**:
- Può creare `DinnerAvailability` con `can_host = false` per comunicare la propria disponibilità al gruppo
  - `AVAILABLE`: disponibile a partecipare
  - `NOT_AVAILABLE`: non disponibile per quella data
- Crea `DinnerBooking` per prenotare presso un host
- Stati prenotazione: `PENDING` → `CONFIRMED` → `CANCELLED`

### Flusso Prenotazione

1. **Host dichiara disponibilità**: Crea `DinnerAvailability` con `can_host=true`, `max_guests=N`
2. **Guest visualizza nel calendario**: Vede disponibilità host nel pannello GroupAvailabilities
3. **Guest prenota**: Crea `DinnerBooking` specificando:
   - Numero ospiti aggiuntivi (`guests_count`)
   - Cosa porta (`bringing_items`)
   - Note/allergie (`notes`)
4. **Sistema aggiorna automaticamente** (via `DinnerBookingObserver`):
   - Prenotazione creata con status `PENDING` (o `CONFIRMED`)
   - Totale ospiti prenotati ricalcolato per l'host
   - Status host aggiornato: `AVAILABLE_TO_HOST` → `ALMOST_FULL` → `FULL`

**Nota importante**: Lo stato della `DinnerAvailability` del guest rimane `AVAILABLE` - la prenotazione è tracciata tramite il modello `DinnerBooking`.

## Stati Sistema

### DinnerAvailabilityStatus (7 stati)

**Host (can_host = true):**
- `AVAILABLE_TO_HOST`: Disponibile ad ospitare, può ricevere prenotazioni
- `ALMOST_FULL`: Vicino al limite massimo di ospiti, accetta ancora prenotazioni
- `FULL`: Raggiunto il massimo di ospiti, non accetta più prenotazioni
- `HOST_CANCELLED`: Host ha cancellato la disponibilità (con motivo specificato)
- `COMPLETED`: Cena conclusa, impostato automaticamente dal cron job giornaliero

**Guest (can_host = false):**
- `AVAILABLE`: Disponibile a partecipare come ospite
- `NOT_AVAILABLE`: Non disponibile per quella data (comunica assenza al gruppo)

### DinnerBookingStatus (3 stati)

- `PENDING`: In attesa di conferma dall'host
- `CONFIRMED`: Confermata dall'host
- `CANCELLED`: Cancellata (dall'host o dal guest)

## Transizioni Stati Automatiche

### Host Availability (gestite da DinnerBookingObserver)

```
AVAILABLE_TO_HOST
  ↓ (prima prenotazione confermata)
ALMOST_FULL
  ↓ (posti esauriti: total_booked_guests >= max_guests)
FULL
  ↑ (prenotazioni cancellate, tornano posti)
ALMOST_FULL
  ↑ (tutte prenotazioni cancellate)
AVAILABLE_TO_HOST
```

**Nota**: Lo stato `HOST_CANCELLED` è manuale e non viene mai sovrascritto dall'Observer.

### Prenotazioni

```
PENDING (creazione)
  ↓ (host conferma)
CONFIRMED
  ↓ (host o guest cancella)
CANCELLED

PENDING
  ↓ (host o guest cancella)
CANCELLED
```

## Regole di Business

### Validazioni Prenotazione (DinnerBookingPolicy.book)

Un guest può prenotare solo se **tutte** queste condizioni sono soddisfatte:

1. Utente appartiene a un gruppo
2. Non sta prenotando la propria disponibilità (non può essere host e guest della stessa cena)
3. È nello stesso gruppo dell'host
4. Disponibilità è di tipo host (`can_host = true`)
5. Status disponibilità accetta prenotazioni (`AVAILABLE_TO_HOST` o `ALMOST_FULL`)
6. Ci sono posti disponibili (`available_spots > 0`)
7. Non ha già prenotato questa disponibilità (in qualsiasi stato)
8. **Non ha altre prenotazioni nello stesso giorno** (incluse quelle `CANCELLED`)

### Gestione Cancellazioni

**Disponibilità Host**:
- **Senza prenotazioni**: Può eliminare completamente (hard delete)
- **Con prenotazioni** (qualsiasi stato): Non può eliminare, deve impostare status `HOST_CANCELLED`
  - Tutte le prenotazioni attive vengono automaticamente cancellate (via `DinnerAvailabilityObserver`)
  - Notifica inviata a tutti i guest con prenotazioni attive

**Prenotazioni Guest**:
- **Non può mai eliminare** (no hard delete)
- Può solo impostare status `CANCELLED` tramite form
- La prenotazione cancellata diventa read-only
- Posti liberati e status host aggiornato automaticamente

### Dati Storici (Read-Only)

**Disponibilità `COMPLETED`**:
- Non modificabile né eliminabile
- Form mostrato in sola lettura
- Creato automaticamente dal comando schedulato `CompleteExpiredAvailabilities` (daily alle 02:00)

**Prenotazioni per cene completate**:
- Non modificabili se `hostAvailability->status === COMPLETED`
- Form in sola lettura

## Logging Eventi

Il sistema traccia tutti gli eventi tramite il modello `DinnerLog`:

### Eventi Disponibilità (DinnerAvailabilityObserver)
- `created`: Creazione nuova disponibilità
- `status_changed`: Cambio stato (include old_status, new_status, cancellation_reason)
- `dinner_name_changed`: Modifica nome cena
- `max_guests_changed`: Modifica capacità massima
- `note_changed`: Modifica note
- `host_cancelled_cascade`: Cancellazione host con cancellazione automatica prenotazioni

### Eventi Prenotazione (DinnerBookingObserver)
- `created`: Creazione nuova prenotazione
- `status_changed`: Cambio stato prenotazione
- `guests_count_changed`: Modifica numero ospiti
- `bringing_items_changed`: Modifica items portati
- `notes_changed`: Modifica note

## Notifiche

**`DinnerCancelledByHostNotification`**:
- Inviata quando host cancella disponibilità (`status → HOST_CANCELLED`)
- Destinatari: Tutti i guest con prenotazioni attive (PENDING o CONFIRMED)
- Canale: Database notification
- Dati: availability details, booking details, host_name, date, cancellation_reason

## Scheduled Jobs

**`CompleteExpiredAvailabilities` Command**:
- Schedule: Daily alle 02:00 (Europe/Rome)
- Funzione: Imposta status `COMPLETED` per disponibilità con date passate
- Cancella prenotazioni non confermate per date passate

## UI Principale

### Calendario Gruppo (GroupAvailabilities)
- Visualizzazione mensile/settimanale
- Badge colorati per host (verde) e guest (rosa)
- Info posti: "Posti: X/Y (Z liberi)" o "(PIENO)"
- Pulsante "Prenota" visibile solo se tutte le condizioni sono soddisfatte
- Badge prenotazioni esistenti dell'utente:
  - Verde: CONFIRMED
  - Arancione: PENDING
  - Rosso: CANCELLED
  - Link diretto alla pagina di modifica prenotazione
- Filtri per status e capacità

### Risorse Filament

**DinnerAvailabilityResource**:
- CRUD completo disponibilità
- ViewAction per visualizzazione read-only
- Form disabilitato se `COMPLETED` o data passata
- Relation Manager per prenotazioni ricevute (se host)

**DinnerBookingResource**:
- CRUD completo prenotazioni
- Form disabilitato se `CANCELLED`, `COMPLETED` o data passata
- Validazione capacità in real-time
- No DeleteAction (solo soft delete via campo status)

## Database

### dinner_availabilities
```
- id
- dinner_date_id (FK → dinner_dates)
- user_id (FK → users)
- can_host (boolean)
- max_guests (int, nullable - solo per host)
- dinner_name (string, nullable - solo per host)
- status (enum DinnerAvailabilityStatus)
- note (text, nullable)
- cancellation_reason (enum CancellationReason, nullable)
- UNIQUE(dinner_date_id, user_id)
```

### dinner_bookings
```
- id
- host_availability_id (FK → dinner_availabilities)
- guest_user_id (FK → users)
- guests_count (int - ospiti aggiuntivi oltre al guest)
- bringing_items (json, nullable)
- notes (text, nullable)
- status (enum DinnerBookingStatus)
- UNIQUE(host_availability_id, guest_user_id)
```

### dinner_logs
```
- id
- logged_by (FK → users, nullable - null per eventi di sistema)
- loggable_type (polymorphic)
- loggable_id (polymorphic)
- availability_id (FK → dinner_availabilities)
- status (string)
- metadata (json)
- created_at
```

## Test Suite

**123 test** distribuiti su 7 file (Pest v3):
- `DinnerAvailabilityTest` (25 test)
- `DinnerBookingTest` (16 test)
- `DinnerAvailabilityObserverTest` (16 test)
- `DinnerBookingObserverTest` (19 test)
- `DinnerAvailabilityPolicyTest` (16 test)
- `DinnerBookingPolicyTest` (24 test)
- `DinnerCancelledByHostNotificationTest` (7 test)

Copertura: Models, Observers, Policies, Notifications

## Comandi Utili

```bash
# Run tests
docker-compose exec app vendor/bin/pest

# Run scheduled command manually
docker-compose exec app php artisan availabilities:complete-expired

# Code formatting
docker-compose exec app vendor/bin/duster fix
```
