# Piano Implementazione Sistema Prenotazioni Cene

## Obiettivo
Implementare un sistema di prenotazioni che permetta agli utenti di prenotare cene quando altri membri del gruppo si offrono come host (can_host=true). Il sistema deve gestire la capacità massima di ospiti e cambiare automaticamente lo stato dell'host a BOOKED quando necessario.

## PREREQUISITI - Revisione DinnerAvailabilityStatus

### Nuova Struttura Enum

**Per `can_host = true` (chi ospita):**
1. `AVAILABLE_TO_HOST` - "Disponibile ad ospitare" (verde) - nessuna prenotazione ancora
2. `ALMOST_FULL` - "Quasi pieno" (arancione/warning) - ha prenotazioni ma ci sono ancora posti ( almeno un posto confermato )
3. `FULL` - "Pieno" (rosso scuro) - tutti i posti occupati, non accetta più prenotazioni
4. `HOST_CANCELLED` - "Annullato" (grigio) - l'host ha cancellato la disponibilità

**Per `can_host = false` (chi partecipa):**
1. `AVAILABLE` - "Disponibile" (verde chiaro) - disponibile a partecipare
2. `BOOKED` - "Prenotato" (viola) - ha prenotato una cena come guest
3. `UNAVAILABLE` - "Non disponibile" (rosso) - non può partecipare

### Diagramma Cambi di Stato

**Per HOST (can_host = true):**
```
AVAILABLE_TO_HOST
    ↓ (prima prenotazione arriva)
ALMOST_FULL
    ↓ (posti si esauriscono completamente)
FULL
    ↑ (prenotazioni cancellate, tornano posti disponibili)
ALMOST_FULL
    ↑ (tutte le prenotazioni cancellate)
AVAILABLE_TO_HOST

AVAILABLE_TO_HOST / ALMOST_FULL / FULL
    ↓ (host cancella manualmente)
HOST_CANCELLED
```

**Per GUEST (can_host = false):**
```
AVAILABLE
    ↓ (prenota una cena da qualcuno)
BOOKED
    ↑ (cancella la prenotazione)
AVAILABLE

AVAILABLE
    ↓ (decide di non unirsi a nessuna cena)
UNAVAILABLE
    ↑ (cambia idea)
AVAILABLE
```

### Regole di Transizione

**Automatiche (gestite da Observer):**
- `AVAILABLE_TO_HOST` → `ALMOST_FULL` quando arriva la prima prenotazione
- `ALMOST_FULL` → `FULL` quando `total_booked_guests >= max_guests`
- `FULL` → `ALMOST_FULL` quando `total_booked_guests < max_guests` (dopo cancellazione)
- `ALMOST_FULL` → `AVAILABLE_TO_HOST` quando `total_booked_guests = 0` (tutte cancellate)
- `AVAILABLE` → `BOOKED` quando guest crea una prenotazione
- `BOOKED` → `AVAILABLE` quando guest cancella la prenotazione

**Manuali (utente sceglie):**
- `AVAILABLE_TO_HOST/ALMOST_FULL/FULL` → `HOST_CANCELLED` (host cancella)
- `AVAILABLE` ↔ `UNAVAILABLE` (guest cambia disponibilità)
- `AVAILABLE` ↔ `MAYBE` (guest non è sicuro)

## Requisiti Principali
1. **Pulsante "Prenota"** visibile solo su disponibilità con:
   - `can_host = true`
   - `status = AVAILABLE_TO_HOST` o `status = ALMOST_FULL` (non FULL o HOST_CANCELLED)
   - Posti disponibili dall'host (`available_spots > 0`)
   - Non è la propria disponibilità
   - Guest non ha già altre prenotazioni confermate nello stesso giorno

2. **Tabella `dinner_bookings`** con campi:
   - `guests_count` (numero ospiti aggiuntivi portati)
   - `bringing_items` (cosa porta il guest)
   - `notes` (note/allergie)
   - Relazioni con disponibilità host e guest

3. **Validazione capacità**: Impedire prenotazione se `guests_count + prenotazioni_esistenti > max_guests` dell'host

4. **Cambio stato automatico**:
   - HOST: `AVAILABLE_TO_HOST` → `ALMOST_FULL` (prima prenotazione) → `FULL` (posti esauriti)
   - GUEST: `AVAILABLE` → `BOOKED` (quando prenota) → `AVAILABLE` (quando cancella)

## Struttura Dati

### Tabella `dinner_bookings`
```
- id
- host_availability_id (FK -> dinner_availabilities)
- guest_user_id (FK -> users)
- guests_count (int: ospiti aggiuntivi oltre al guest)
- bringing_items (text nullable)
- notes (text nullable)
- status (enum: confirmed/cancelled)
- timestamps
- UNIQUE(host_availability_id, guest_user_id)
```

**IMPORTANTE:** Un utente NON può avere più prenotazioni come ospite nello stesso giorno. Questo vincolo deve essere implementato a livello di validazione/policy.



### Relazioni
- DinnerBooking → DinnerAvailability (host)
- DinnerBooking → User (guest)
- DinnerAvailability → hasMany DinnerBooking
- User → hasMany DinnerBooking (come guest ma non nello stesso giorno)

## Implementazione Step-by-Step

### ✅ STEP 0: Aggiornamento Enum DinnerAvailabilityStatus (COMPLETATO)
**File modificati:**
1. ✅ `app/Enums/DinnerAvailabilityStatus.php` - aggiornato con nuovi stati:
   - ✅ Aggiunti: `AVAILABLE_TO_HOST`, `ALMOST_FULL`, `FULL`, `HOST_CANCELLED`
   - ✅ Mantenuti: `AVAILABLE`, `BOOKED`, `UNAVAILABLE`
   - ✅ Rimosso: `CANCELLED` (sostituito da `HOST_CANCELLED`) e `MAYBE`
   - ✅ Aggiornati labels, colors, icons per tutti gli stati
   - ✅ Aggiunti metodi helper: `isHostStatus()`, `isGuestStatus()`, `canAcceptBookings()`

2. ✅ `database/seeders/DinnerDatesSeeder.php` - aggiornato per usare i nuovi stati
   - ✅ HOST: solo `AVAILABLE_TO_HOST` come stato iniziale
   - ✅ GUEST: 85% `AVAILABLE`, 15% `UNAVAILABLE`
   - ✅ Stati automatici (ALMOST_FULL, FULL, BOOKED) gestiti dall'Observer
   - ✅ max_guests random tra 4-10 per host

**Risultato:**
- ✅ Enum con 7 stati separati per host (4) e guest (3)
- ✅ Transizioni automatiche gestibili da Observer
- ✅ Seeder aggiornato per generare solo stati iniziali validi
- ✅ Colore personalizzato `purple` aggiunto al panel Filament

### ✅ STEP 1: Database e Modelli Base (COMPLETATO)
**File creati:**
1. ✅ `database/migrations/2025_12_21_161343_create_dinner_bookings_table.php` - migrata con successo
2. ✅ `database/migrations/2025_12_21_162346_add_max_guests_to_dinner_availabilities_table.php` - migrata con successo
3. ✅ `app/Models/DinnerBooking.php` - con relazioni e scopes

**File modificati:**
4. ✅ `app/Models/DinnerAvailability.php` - aggiunta relazione `bookings()` e metodi helper:
   - `confirmedBookings()`
   - `getTotalBookedGuestsAttribute()`
   - `hasAvailableSpots()`
   - `getAvailableSpotsAttribute()`
   - ✅ `canAcceptBookings()` - verifica se status = AVAILABLE_TO_HOST o ALMOST_FULL
   - ✅ Aggiunto campo `max_guests` a fillable e casts

5. ✅ `app/Models/User.php` - aggiunta relazione `guestBookings()`

6. ✅ `app/Filament/App/Resources/DinnerAvailabilities/Schemas/DinnerAvailabilityForm.php`:
   - ✅ Filtro dinamico stati in base a can_host (usa `isHostStatus()` e `isGuestStatus()`)
   - ✅ Campo `max_guests` visibile solo per host con `->dehydrated()`
   - ✅ Default `max_guests` dal profilo utente
   - ✅ Cambio automatico status quando si toglie/mette can_host

7. ✅ `app/Providers/Filament/AppPanelProvider.php` - aggiunto colore custom `purple`

**Risultato:**
- ✅ Tabella `dinner_bookings` creata con tutti i campi e constraints
- ✅ Tabella `dinner_availabilities` estesa con campo `max_guests`
- ✅ Modello DinnerBooking con relazioni hostAvailability e guest
- ✅ Modello DinnerAvailability con metodi helper per calcolare posti disponibili
- ✅ Aggiornato `booted()` per validare coerenza tra can_host e status
- ✅ User model con relazione alle prenotazioni come guest
- ✅ Form con state machine semplice per gestione stati
- ✅ Colore purple disponibile in Filament

### ✅ STEP 2: Business Logic e Validazioni (COMPLETATO)
**File creati:**
1. ✅ `app/Rules/ValidateBookingCapacity.php` - validazione capacità con supporto per edit
2. ✅ `app/Observers/DinnerBookingObserver.php` - gestisce transizioni automatiche status:
   - ✅ HOST: AVAILABLE_TO_HOST → ALMOST_FULL → FULL (e viceversa)
   - ✅ GUEST: AVAILABLE → BOOKED (e viceversa)
   - ✅ Gestisce eventi: created, updated, deleted
   - ✅ Usa `saveQuietly()` per evitare loop infiniti

**File da modificare:**
3. ⏳ `app/Providers/AppServiceProvider.php` - registrare observer nella boot() (PROSSIMO)

**Risultato:**
- ✅ ValidateBookingCapacity verifica posti disponibili considerando guests_count + 1
- ✅ Observer gestisce automaticamente tutti i cambi di stato
- ✅ Non modifica status HOST_CANCELLED (manuale)
- ⏳ Manca solo registrazione observer in AppServiceProvider

### STEP 3: Autorizzazioni
**File da creare:**
10. `app/Policies/DinnerBookingPolicy.php` - policy completa per bookings con metodo `book()`

**File da modificare:**
11. `app/Policies/DinnerAvailabilityPolicy.php` - aggiornare metodo `delete()` per impedire cancellazione se ci sono prenotazioni

**Cosa fa:**
- Definisce chi può vedere/creare/modificare/cancellare prenotazioni
- Metodo speciale `book()` verifica tutte le condizioni per prenotare:
  - Non è la propria disponibilità
  - Stesso gruppo
  - can_host = true
  - status = `AVAILABLE_TO_HOST` o `ALMOST_FULL` (non FULL o HOST_CANCELLED)
  - Ci sono posti disponibili (`available_spots > 0`)
  - Non ha già prenotato questa disponibilità
  - **Non ha altre prenotazioni confermate nello stesso giorno**
- Impedisce cancellazione disponibilità con prenotazioni attive

### STEP 4: Interfaccia Calendario
**File da modificare:**
12. `app/Filament/App/Pages/GroupAvailabilities.php` - aggiungere:
    - Proprietà pubbliche per gestire modal (`$bookingAvailabilityId`, `$bookingData`)
    - Metodo `canBook(int $availabilityId): bool`
    - Metodo `openBookingModal(int $availabilityId): void`
    - Metodo `createBooking(array $data): void`
    - Modificare `loadCalendarData()` per includere info prenotazioni nelle availabilities
    - Aggiornare colori/badge per visualizzare nuovi stati

13. `resources/views/filament/app/pages/group-availabilities.blade.php` - aggiungere:
    - Pulsante "Prenota" nel loop delle availabilities (visibile solo se `can_book = true`)
    - Mostrare info posti (es: "Posti: 3/8") per chi può ospitare con status AVAILABLE_TO_HOST o ALMOST_FULL
    - Badge colorati per distinguere stati: verde (AVAILABLE_TO_HOST), arancione (ALMOST_FULL), rosso (FULL)
    - Modal di prenotazione con form (guests_count, bringing_items, notes)
    - Script per aprire modal via Livewire event

**Cosa fa:**
- Mostra pulsante "Prenota" solo dove possibile
- Apre modal con form per inserire dati prenotazione
- Gestisce validazione e creazione prenotazione
- Aggiorna calendario dopo prenotazione

### STEP 5: Risorsa Filament (Opzionale ma Consigliato)
**File da creare:**
14. `app/Filament/App/Resources/DinnerBookings/DinnerBookingResource.php`
15. `app/Filament/App/Resources/DinnerBookings/Schemas/DinnerBookingForm.php`
16. `app/Filament/App/Resources/DinnerBookings/Tables/DinnerBookingsTable.php`
17. `app/Filament/App/Resources/DinnerBookings/Pages/ListDinnerBookings.php`
18. `app/Filament/App/Resources/DinnerBookings/Pages/CreateDinnerBooking.php`
19. `app/Filament/App/Resources/DinnerBookings/Pages/EditDinnerBooking.php`
20. `app/Filament/App/Resources/DinnerBookings/Pages/ViewDinnerBooking.php`

**Cosa fa:**
- Permette gestione completa prenotazioni tramite panel Filament
- Pagina "Le Mie Prenotazioni" nel menu
- Lista con filtri, create/edit/view
- Form alternativo al modal del calendario

### STEP 6: Testing
**File da creare:**
21. `tests/Unit/Models/DinnerBookingTest.php`
22. `tests/Unit/Rules/ValidateBookingCapacityTest.php`
23. `tests/Unit/Observers/DinnerBookingObserverTest.php`
24. `tests/Feature/DinnerBooking/BookingCreationTest.php`
25. `tests/Feature/DinnerBooking/BookingPolicyTest.php`
26. `tests/Feature/DinnerBooking/BookingCapacityTest.php`
27. `tests/Feature/DinnerBooking/StatusTransitionsTest.php` - testa i cambi di stato automatici

**Cosa fa:**
- Verifica funzionamento modelli e relazioni
- Testa validazioni e business logic
- Verifica autorizzazioni e edge cases

## File Critici

### Da Modificare PRIMA (Priorità Massima - STEP 0)
1. `app/Enums/DinnerAvailabilityStatus.php` - aggiornare enum con nuovi stati

### Da Creare (Priorità Alta)
1. `database/migrations/*_create_dinner_bookings_table.php`
2. `app/Models/DinnerBooking.php`
3. `app/Rules/ValidateBookingCapacity.php`
4. `app/Observers/DinnerBookingObserver.php`
5. `app/Policies/DinnerBookingPolicy.php`

### Da Modificare (Priorità Alta)
1. `app/Models/DinnerAvailability.php` - relazioni e metodi helper
2. `app/Filament/App/Pages/GroupAvailabilities.php` - logica prenotazione
3. `resources/views/filament/app/pages/group-availabilities.blade.php` - UI pulsante e modal
4. `app/Providers/AppServiceProvider.php` - registrare observer
5. `app/Policies/DinnerAvailabilityPolicy.php` - proteggere delete

### Opzionali (Da Implementare Successivamente)
- Risorsa Filament completa per gestione bookings
- Notifiche email quando si riceve/cancella prenotazione
- Dashboard con statistiche prenotazioni
- Export/report per host

## Edge Cases Gestiti

1. **Capacità superata**: Validazione impedisce prenotazione
2. **Prenotazione duplicata**: Constraint UNIQUE impedisce doppie prenotazioni
3. **Cancellazione host**: Policy impedisce se ci sono prenotazioni attive
4. **Cambi status automatici**:
   - HOST: AVAILABLE_TO_HOST → ALMOST_FULL → FULL (e viceversa)
   - GUEST: AVAILABLE → BOOKED (e viceversa)
5. **Stesso gruppo**: Policy verifica appartenenza al gruppo
6. **Non propria disponibilità**: Policy impedisce prenotare se stessi
7. **Una prenotazione al giorno per guest**: Validazione/Policy impedisce che un utente prenoti più cene come ospite nello stesso giorno
8. **Stati validi per prenotazione**: Pulsante "Prenota" visibile solo per AVAILABLE_TO_HOST o ALMOST_FULL (non FULL/HOST_CANCELLED)

## Note Implementative

- **Observer quietSave**: Usare `saveQuietly()` per evitare loop di eventi
- **Total guests**: `guests_count + 1` (il guest stesso conta)
- **Filtri calendario**: Mantenere funzionanti con nuove info prenotazioni
- **Modal Livewire**: Usare eventi browser per aprire/chiudere
- **Validazione real-time**: Form valida capacità durante digitazione

## Prossimi Step dopo Implementazione

1. Sistema notifiche email
2. Dashboard statistiche host/guest
3. Calendario iCal export
4. Sistema feedback post-cena
5. Chat/messaggi tra host e guests
