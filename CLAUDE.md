# CLAUDE.md

Guida per Claude Code su questo progetto.

## Project Overview: DinnerTable

**DinnerTable** è un'applicazione web per la gestione e coordinamento di cene di gruppo. Permette agli utenti di organizzarsi in gruppi, dichiarare disponibilità ad ospitare o partecipare a cene, e gestire prenotazioni.

### Core Concept

1. **Disponibilità (DinnerAvailability)** - Due ruoli:
   - **Host**: dichiara disponibilità ad ospitare per una data specifica con numero max ospiti
   - **Guest**: dichiara disponibilità a partecipare come ospite

2. **Prenotazioni (DinnerBooking)** - I guest prenotano presso gli host disponibili

3. **Gestione Stati** - Sistema automatico:
   - Host: AVAILABLE_TO_HOST → ALMOST_FULL → FULL (in base a prenotazioni)
   - Cene passate: automaticamente completate dal cron job giornaliero alle 02:00

### User Panels

1. **Admin Panel** (`/admin`) - Gestione completa sistema (utenti, gruppi)
2. **App Panel** (`/dinner`) - Pannello utenti finali per gestione disponibilità e prenotazioni

**IMPORTANTE**: Root path `/` è riservato alla landing page pubblica (non Filament).

### Registration & Onboarding Flow

Wizard obbligatorio `CompleteProfile` (middleware `EnsureProfileIsComplete`):
1. Dati personali (city, address, house_number, postal_code)
2. Capacità hosting (max_guests)
3. Accettazione privacy policy

### DinnerGroup System

- Codice univoco 14 caratteri alfanumerici
- Ogni utente appartiene a **un solo gruppo** alla volta (dinner_group_id nullable)
- Creatore (created_by) con badge speciale

### Business Logic Automatica

**Observers** (CRITICI - non bypassare):
1. **DinnerAvailabilityObserver**: aggiorna status host in base a prenotazioni
2. **DinnerBookingObserver**: valida capacità e previene overbooking

**Policies**:
- DinnerAvailabilityPolicy
- DinnerBookingPolicy

### Scheduled Jobs

**CompleteExpiredAvailabilities Command**:
- Schedule: Daily alle 02:00 (Europe/Rome)
- Imposta status COMPLETED per disponibilità scadute
- Cancella prenotazioni non confermate

## Development Commands

### Docker Setup
**SEMPRE usare docker-compose per artisan**:
```bash
docker-compose exec --user $(id -u):$(id -g) app php artisan [command]
```

### Quick Commands
```bash
composer setup    # Setup completo
composer dev      # Dev environment (server, queue, pail, vite)
composer test     # Run tests
npm run dev       # Vite HMR
npm run build     # Production build
vendor/bin/duster fix  # Code formatting (SEMPRE prima di commit)
```

## Architecture

### Dual Panel Architecture (Filament v4)

**Admin Panel** (`/admin`):
- UserResource (gestione utenti, verifica email, promozione admin)
- DinnerGroupResource con MembersRelationManager

**App Panel** (`/dinner`, panel ID: `dinner`):
- DinnerAvailabilityResource
- DinnerBookingResource
- Pages: CompleteProfile, ManageDinnerGroup, GroupAvailabilities, TutorialPage, EditProfile, LeaveReview
- Auth Pages: Login, Register (custom views con link aggiuntivi e stili responsive)

**Middleware**: `EnsureProfileIsComplete` forza completamento profilo.

### Tech Stack

- **PHP 8.4** + **Laravel 12** + **Filament v4**
- **Livewire v3** + **Alpine.js** + **Tailwind CSS v4**
- **Laravel Reverb** per WebSocket
- **SQLite** (development)
- **Pest v3** per testing

### Database Schema

**users**: id, name, email, password, is_admin, dinner_group_id, email_verified_at

**profiles** (1:1 con User): user_id, city, address, house_number, postal_code, max_guests, privacy_accepted_at, avatar_url

**dinner_groups**: id, name, slogan, group_image, group_code (14 chars unique), created_by

**dinner_dates**: id, dinner_group_id, dinner_date (unique constraint)

**dinner_availabilities**: id, dinner_date_id, user_id, status (enum), can_host, max_guests, note, cancellation_reason (unique: dinner_date_id + user_id)

**dinner_bookings**: id, host_availability_id, guest_user_id, guests_count, bringing_items (json), notes, status (unique: host_availability_id + guest_user_id)

**app_reviews**: id, user_id (unique), rating (0-5), comment (nullable), timestamps

### Enums

**DinnerAvailabilityStatus** (7 stati):
- AVAILABLE_TO_HOST, ALMOST_FULL, FULL, HOST_CANCELLED, COMPLETED (host)
- AVAILABLE, NOT_AVAILABLE (guest)

**DinnerBookingStatus**: PENDING, CONFIRMED, CANCELLED

**CancellationReason**: motivi cancellazione disponibilità

## Project Structure

```
app/
├── Console/Commands/CompleteExpiredAvailabilities.php
├── Enums/
├── Filament/
│   ├── Admin/Resources/ (UserResource, DinnerGroupResource)
│   └── App/
│       ├── Resources/ (DinnerAvailabilityResource, DinnerBookingResource)
│       ├── Pages/ (CompleteProfile, ManageDinnerGroup, GroupAvailabilities, LeaveReview)
│       └── Auth/Pages/ (Login, Register)
├── Forms/Components/ (RatingStar custom component)
├── Models/ (User, Profile, DinnerGroup, DinnerDate, DinnerAvailability, DinnerBooking, AppReview)
├── Observers/ (DinnerAvailabilityObserver, DinnerBookingObserver)
└── Policies/
```

## Important Notes

### Filament Multi-Panel
```bash
# Creare resources
php artisan make:filament-resource ModelName --panel=admin
php artisan make:filament-resource ModelName --panel=dinner
```

### Availability & Booking Flow

**Host**:
1. Crea DinnerAvailability con can_host=true
2. Status auto-aggiornato da Observer: AVAILABLE_TO_HOST → ALMOST_FULL → FULL
3. Cron job imposta COMPLETED per date passate

**Guest**:
1. Visualizza calendario (GroupAvailabilities page)
2. Crea DinnerBooking (guests_count, bringing_items, notes)
3. Status: PENDING → host conferma → CONFIRMED

**Validazioni automatiche**: DinnerBookingObserver previene overbooking.

### Calendar UI Features (GroupAvailabilities)
- Toggle vista: Mensile / Settimanale
- Navigazione mesi/settimane
- Filtri per status e capacità
- Prenotazione diretta da calendario
- Real-time updates via Reverb

### Review System (LeaveReview)
- Ogni utente può lasciare **una sola recensione** (unique constraint su user_id)
- Rating 0-5 stelle con custom component `RatingStar`
- Commento opzionale
- Reviews visualizzate nella landing page (top 4 con rating >= 4)
- Accessibile da user menu nel pannello app

### Custom Auth Pages
- Login e Register personalizzate per App Panel
- Link aggiuntivi: navigazione tra login/register, ritorno alla home
- **Mobile responsive**: full viewport width/height su mobile
- CSS custom per centraggio verticale e ottimizzazione touch (font-size 16px previene zoom iOS)

## Code Quality Standards

### Import Best Practices
**CRITICAL**: Sempre importare classi all'inizio del file, MAI usare FQCN.

✅ **CORRETTO**:
```php
use Carbon\Carbon;
use App\Enums\DinnerAvailabilityStatus;
use Filament\Tables\Columns\TextColumn;

$date = Carbon::parse($value);
```

❌ **ERRATO**:
```php
$date = \Carbon\Carbon::parse($value);  // NO!
```

**Tool di verifica**:
```bash
docker-compose exec app vendor/bin/duster fix
```

### PHP DocBlocks
Documentare SEMPRE:
- Classi (descrizione, funzionalità, @see tags)
- Metodi pubblici/protected (descrizione, @param, @return, @throws)
- Proprietà importanti

**Lingua**: Italiano per business logic, inglese per termini tecnici.

### Custom Filament Components

**RatingStar Component** (`app/Forms/Components/RatingStar.php`):
- Estende `Filament\Forms\Components\Field`
- View: `resources/views/forms/components/rating-star.blade.php`
- Utilizza SVG Tabler icons (`tabler-star-filled`)
- CSS: reverse flex-direction per highlight corretto (da destra a sinistra)

Esempio uso:
```php
use App\Forms\Components\RatingStar;

RatingStar::make('rating')
    ->required()
    ->maxStars(5)
```

### Filament v4 Best Practices

**Form Definition**: Usare `Schema` non `Form`
```php
// ✅ CORRETTO (Filament v4)
public function form(Schema $schema): Schema {
    return $schema->schema([...]);
}

// ❌ ERRATO
public function form(Form $form): Form {
    return $form->schema([...]);
}
```

**SVG Rendering in Tables**:
```php
// ✅ CORRETTO
TextColumn::make('field')
    ->description(fn($record) =>
        svg('tabler-icon', 'w-4 h-4')->toHtml() . ' text'
    )
    ->html()

// ❌ ERRATO (mostra "@svg" letterale)
->description("@svg('tabler-icon') text")
```

## Development Workflow

Ordine per nuove feature:
1. Migrations
2. Models + relationships
3. Filament resources/pages
4. Policies
5. Seeders
6. Tests (PEST)
7. `vendor/bin/duster fix` prima di commit

**Non chiedere mai permesso per usare docker-compose** dentro il progetto Laravel.
