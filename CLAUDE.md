# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview: DinnerTable

**DinnerTable** è un'applicazione web per la gestione e coordinamento di cene di gruppo. Permette agli utenti di organizzarsi in gruppi, dichiarare disponibilità ad ospitare o partecipare a cene, e gestire prenotazioni.

### Core Concept
Gli utenti si registrano, creano o si uniscono a un gruppo cena (DinnerGroup), e partecipano alla pianificazione delle cene. Il sistema si basa su:

1. **Disponibilità (DinnerAvailability)** - Due ruoli:
   - **Host**: dichiara disponibilità ad ospitare per una data specifica con numero max ospiti
   - **Guest**: dichiara disponibilità a partecipare come ospite

2. **Prenotazioni (DinnerBooking)** - I guest prenotano presso gli host disponibili

3. **Gestione Stati** - Sistema automatico di aggiornamento stati:
   - Host: AVAILABLE_TO_HOST → ALMOST_FULL → FULL (in base a prenotazioni)
   - Cene passate: automaticamente completate dal cron job giornaliero

### User Roles & Panels
1. **Admin Panel** (`/admin`) - Gestione completa sistema (utenti, gruppi)
2. **App Panel** (`/dinner`) - Pannello utenti finali per gestione disponibilità e prenotazioni

### Registration & Onboarding Flow
Quando un utente si registra, deve completare obbligatoriamente il proprio profilo tramite wizard Filament:

1. **Dati Personali & Privacy**:
   - Nome (name)
   - Email con verifica obbligatoria
   - Password
   - Accettazione privacy policy

2. **Profilo Dettagliato** (modello Profile separato):
   - Città (city)
   - Indirizzo (address)
   - Numero civico (house_number)
   - CAP (postal_code)
   - Numero massimo ospiti che può ospitare (max_guests)
   - Avatar opzionale (avatar_url)

Il middleware `EnsureProfileIsComplete` blocca l'accesso finché il wizard non è completato.

### DinnerGroup System
- Gli utenti possono **creare un gruppo** ricevendo un codice univoco di 14 caratteri alfanumerici
- Gli utenti possono **unirsi a un gruppo** tramite codice invito
- Ogni utente appartiene a **un solo gruppo** alla volta (dinner_group_id nullable)
- Struttura gruppo:
  - Nome, slogan, immagine gruppo
  - Creatore (created_by)
  - Lista membri con avatar

### Sistema Disponibilità e Prenotazioni

#### DinnerDate
Ogni data (dinner_date) rappresenta una giornata in cui il gruppo può organizzare cene.
- Unique constraint: (dinner_group_id, dinner_date)
- Contiene multiple DinnerAvailability per quella data

#### DinnerAvailability (Host/Guest)
Disponibilità dichiarata da un membro per una specifica data:

**Come HOST (can_host = true)**:
- Status: AVAILABLE_TO_HOST, ALMOST_FULL, FULL, HOST_CANCELLED, COMPLETED
- max_guests: capacità massima
- Riceve prenotazioni da altri membri
- Status aggiornato automaticamente da DinnerAvailabilityObserver in base a prenotazioni

**Come GUEST (can_host = false)**:
- Status: AVAILABLE, NOT_AVAILABLE
- Comunica al gruppo la propria presenza/assenza
- Può prenotare presso host disponibili

#### DinnerBooking
Prenotazione effettuata da un guest presso un host:
- guests_count: numero ospiti (guest + accompagnatori)
- bringing_items: array di oggetti che il guest porterà (es. vino, dolce)
- Status: PENDING, CONFIRMED, CANCELLED
- Validazione automatica capacità tramite DinnerBookingObserver

### Scheduled Jobs (Cron)
Il sistema richiede Laravel scheduler attivo:

**CompleteExpiredAvailabilities Command**:
- **Schedule**: Daily alle 02:00 (Europe/Rome timezone)
- **Funzione**: Completa automaticamente le disponibilità con dinner_date passata
- **Azioni**:
  - Imposta status COMPLETED per disponibilità scadute
  - Cancella prenotazioni non confermate associate

### Business Logic Automatica

#### Observers
1. **DinnerAvailabilityObserver**:
   - Aggiorna status host in base al numero di prenotazioni confermate
   - Sincronizza can_host con status appropriato
   - Gestisce transizioni di stato

2. **DinnerBookingObserver**:
   - Valida capacità disponibile prima di creare/aggiornare prenotazioni
   - Aggiorna automaticamente lo status della disponibilità host
   - Previene overbooking

#### Policies
- **DinnerAvailabilityPolicy**: controllo accessi per creare/modificare disponibilità
- **DinnerBookingPolicy**: validazione prenotazioni (capacità, autorizzazioni)

## Development Commands

### Docker Setup
This project runs in Docker. All artisan commands must be executed through docker-compose:

```bash
docker-compose exec --user $(id -u):$(id -g) app php artisan [command]
```

### Setup
```bash
composer setup
```
This runs the full setup: composer install, creates .env from .env.example, generates app key, runs migrations, installs npm packages, and builds assets.

### Development Server
```bash
composer dev
```
Runs a concurrent development environment with:
- PHP artisan serve (server)
- Queue listener
- Pail logs
- Vite dev server with HMR

Alternatively, run services individually:
```bash
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
npm run dev
```

### Frontend
```bash
npm run dev    # Development with Vite HMR
npm run build  # Production build
```

### Testing
```bash
composer test                # Run all tests
php artisan test             # Run PHPUnit tests directly
php artisan test --filter=TestName  # Run specific test
```

### Code Quality
```bash
vendor/bin/pint              # Laravel Pint (code formatting)
vendor/bin/duster            # Tighten Duster (linting)
```

### Database
```bash
php artisan migrate          # Run migrations
php artisan db:seed          # Run seeders
php artisan migrate:fresh --seed  # Fresh database with seeding
```

## Architecture Overview

### Dual Panel Architecture
This project uses **Filament v4** with two separate panels:

1. **Admin Panel** - Full system administration
   - Path: `/admin`
   - Resources in `app/Filament/Admin/Resources/`
   - Pages in `app/Filament/Admin/Pages/`
   - Widgets in `app/Filament/Admin/Widgets/`
   - Access controlled by FilamentShield permissions
   - Resources:
     - UserResource (gestione utenti, verifica email, promozione admin)
     - DinnerGroupResource (gestione gruppi con MembersRelationManager)

2. **App Panel** - End-user interface
   - Path: `/dinner` (not root)
   - Panel ID: `dinner`
   - Resources in `app/Filament/App/Resources/`
   - Pages in `app/Filament/App/Pages/`
   - Widgets in `app/Filament/App/Widgets/`
   - Resources:
     - DinnerAvailabilityResource (gestione disponibilità host/guest)
     - DinnerBookingResource (gestione prenotazioni)
   - Pages Custom:
     - CompleteProfile (wizard completamento profilo obbligatorio)
     - ManageDinnerGroup (crea/unisciti gruppo, visualizza membri)
     - GroupAvailabilities (calendario con vista mensile/settimanale)
     - TutorialPage (guida uso applicazione)
     - EditProfile (modifica profilo utente)

**Note Importanti**:
- Root path `/` è riservato alla home page pubblica (non Filament)
- Middleware `EnsureProfileIsComplete` forza completamento profilo prima di accedere al panel

### Authentication & Permissions
Uses **Filament Shield** (bezhansalleh/filament-shield) for role and permission management:
- Shield policies in `app/Policies/`
- Permission system managed via `config/filament-shield.php` and `config/permission.php`
- Migration: [database/migrations/2025_12_18_224735_create_permission_tables.php](database/migrations/2025_12_18_224735_create_permission_tables.php)

### Real-time Features
**Laravel Reverb** is configured for WebSocket broadcasting:
- Echo configuration: [resources/js/echo.js](resources/js/echo.js)
- Uses Pusher protocol with Reverb broadcaster
- Broadcast channels defined in [routes/channels.php](routes/channels.php)
- User-specific channel pattern: `App.Models.User.{id}`

### Frontend Stack
- **Vite** for asset bundling with Laravel plugin
- **Tailwind CSS v4** via `@tailwindcss/vite` plugin
- **Laravel Echo** + **Pusher JS** for real-time updates
- HMR configured for localhost with HTTPS
- Vite watches: Livewire components, Filament resources, views

**Entry points**:
- `resources/js/app.js` - Main JavaScript bundle
- `resources/css/app.css` - Main CSS bundle

### Database
Default configuration uses **SQLite** for development:
- Database file: `database/database.sqlite`
- Test environment uses in-memory SQLite
- Queue and cache use database driver by default

### Database Schema (Implemented)

**users** (Laravel default + extensions):
- id, name, email, password, email_verified_at
- is_admin (boolean, default false)
- dinner_group_id (FK nullable a dinner_groups)
- timestamps
- Relations:
  - hasOne Profile
  - belongsTo DinnerGroup
  - hasMany DinnerAvailability
  - hasMany DinnerBooking (as guest)

**profiles** (Profilo separato 1:1 con User):
- id, user_id (FK unique a users)
- city, address, house_number, postal_code
- max_guests (integer)
- privacy_accepted_at (timestamp)
- avatar_url (nullable)
- timestamps
- Relations: belongsTo User

**dinner_groups**:
- id, name, slogan (nullable), group_image (nullable)
- group_code (string 14 chars, unique)
- created_by (FK a users)
- timestamps
- Unique: group_code
- Relations:
  - hasMany User (members)
  - belongsTo User (creator)
  - hasMany DinnerDate

**dinner_dates**:
- id, dinner_group_id (FK), dinner_date (date)
- timestamps
- Unique: (dinner_group_id, dinner_date)
- Relations:
  - belongsTo DinnerGroup
  - hasMany DinnerAvailability

**dinner_availabilities**:
- id, dinner_date_id (FK), user_id (FK)
- status (enum: DinnerAvailabilityStatus)
- can_host (boolean)
- max_guests (integer nullable)
- note (text nullable)
- cancellation_reason (enum nullable: CancellationReason)
- timestamps
- Unique: (dinner_date_id, user_id)
- Relations:
  - belongsTo DinnerDate
  - belongsTo User
  - hasMany DinnerBooking (quando è host)

**dinner_bookings**:
- id, host_availability_id (FK a dinner_availabilities), guest_user_id (FK a users)
- guests_count (integer, include guest + accompagnatori)
- bringing_items (json array)
- notes (text nullable)
- status (enum: DinnerBookingStatus)
- timestamps
- Unique: (host_availability_id, guest_user_id)
- Relations:
  - belongsTo DinnerAvailability (hostAvailability)
  - belongsTo User (guest)

### Enums

**DinnerAvailabilityStatus** (7 stati):
- AVAILABLE_TO_HOST - Host con posti disponibili
- ALMOST_FULL - Host quasi al completo
- FULL - Host al completo
- HOST_CANCELLED - Cancellato dall'host
- COMPLETED - Cena completata (set da cron)
- AVAILABLE - Guest disponibile
- NOT_AVAILABLE - Guest non disponibile

**DinnerBookingStatus** (3 stati):
- PENDING - In attesa conferma
- CONFIRMED - Confermato
- CANCELLED - Cancellato

**CancellationReason** (motivi cancellazione):
- Utilizzato in DinnerAvailability.cancellation_reason

### Queue System
Configured to use database queue driver:
```bash
php artisan queue:listen --tries=1  # Part of composer dev
```

### Scheduled Tasks (Cron)
The application requires Laravel's task scheduler to run:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

**CompleteExpiredAvailabilities Command**:
- **Schedule**: Daily at 02:00 AM (Europe/Rome timezone)
- **Location**: `app/Console/Commands/CompleteExpiredAvailabilities.php`
- **Function**: Completa automaticamente le disponibilità scadute
- **Actions**:
  - Trova tutte le DinnerAvailability con dinner_date < oggi
  - Imposta status = COMPLETED
  - Cancella tutte le prenotazioni non confermate associate

## Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── CompleteExpiredAvailabilities.php  # Daily 02:00 AM job
├── Enums/
│   ├── DinnerAvailabilityStatus.php  # 7 stati disponibilità
│   ├── DinnerBookingStatus.php       # 3 stati prenotazione
│   └── CancellationReason.php        # Motivi cancellazione
├── Filament/
│   ├── Admin/              # Admin panel (/admin)
│   │   ├── Resources/
│   │   │   ├── UserResource.php
│   │   │   └── DinnerGroupResource/
│   │   │       ├── DinnerGroupResource.php
│   │   │       └── RelationManagers/
│   │   │           └── MembersRelationManager.php
│   │   ├── Pages/
│   │   └── Widgets/
│   └── App/                # App panel (/dinner)
│       ├── Resources/
│       │   ├── DinnerAvailabilityResource/
│       │   │   ├── DinnerAvailabilityResource.php
│       │   │   ├── Forms/DinnerAvailabilityForm.php
│       │   │   └── Tables/DinnerAvailabilitiesTable.php
│       │   └── DinnerBookingResource/
│       │       ├── DinnerBookingResource.php
│       │       ├── Forms/DinnerBookingForm.php
│       │       └── Tables/DinnerBookingsTable.php
│       ├── Pages/
│       │   ├── CompleteProfile.php    # Wizard profilo
│       │   ├── ManageDinnerGroup.php  # Gestione gruppo
│       │   ├── GroupAvailabilities.php # Calendario mensile/settimanale
│       │   ├── TutorialPage.php
│       │   └── Auth/
│       │       └── EditProfile.php
│       └── Widgets/
├── Http/
│   ├── Controllers/
│   └── Middleware/
│       └── EnsureProfileIsComplete.php  # Forza completamento profilo
├── Models/
│   ├── User.php              # Base + is_admin + dinner_group_id
│   ├── Profile.php           # Profilo separato 1:1
│   ├── DinnerGroup.php       # Gruppo cena
│   ├── DinnerDate.php        # Date singole
│   ├── DinnerAvailability.php # Disponibilità host/guest
│   └── DinnerBooking.php      # Prenotazioni
├── Observers/
│   ├── DinnerAvailabilityObserver.php  # Auto-update status
│   └── DinnerBookingObserver.php       # Validazione capacità
├── Policies/
│   ├── DinnerAvailabilityPolicy.php
│   └── DinnerBookingPolicy.php
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php  # /admin
        └── AppPanelProvider.php    # /dinner

database/
├── factories/
│   ├── UserFactory.php
│   └── DinnerGroupFactory.php
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── xxxx_create_profiles_table.php
│   ├── xxxx_create_dinner_groups_table.php
│   ├── xxxx_create_dinner_dates_table.php
│   ├── xxxx_create_dinner_availabilities_table.php
│   └── xxxx_create_dinner_bookings_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php
    ├── DinnerGroupSeeder.php
    └── DinnerDatesSeeder.php

resources/
├── css/
│   └── app.css       # Tailwind CSS v4
├── js/
│   ├── app.js        # Main JS entry
│   ├── bootstrap.js  # Axios & Laravel config
│   └── echo.js       # WebSocket/Reverb config
└── views/
    ├── home.blade.php  # Home pubblica
    └── components/
        └── view-toggle.blade.php  # Toggle vista calendario

routes/
├── web.php           # Route home pubblica
├── channels.php      # Broadcast channels
└── console.php       # Scheduled commands
```

## Important Notes

### Filament Multi-Panel Setup
- **Admin Panel**: Use `php artisan make:filament-resource ModelName --panel=admin`
- **App Panel**: Use `php artisan make:filament-resource ModelName --panel=dinner`
- Each panel has separate auto-discovery directories
- Panel access:
  - Admin: `canAccessPanel()` checks `is_admin` flag
  - App: richiede email verificata e profilo completo (middleware)

### User Registration & Profile Completion
- New users si registrano con email + password
- Email verification obbligatoria
- **CompleteProfile wizard** obbligatorio prima di accedere:
  - Step 1: Dati personali (city, address, house_number, postal_code)
  - Step 2: Capacità hosting (max_guests)
  - Step 3: Accettazione privacy
- Middleware `EnsureProfileIsComplete` blocca accesso se profilo incompleto
- Avatar opzionale modificabile da EditProfile page

### DinnerGroup Management
- Ogni utente può appartenere a **un solo gruppo** alla volta
- Group codes: 14 caratteri alfanumerici (unici)
- Creazione gruppo:
  - ManageDinnerGroup page
  - Nome + slogan opzionale + immagine gruppo
  - Auto-join del creatore
- Join gruppo:
  - Inserimento codice invito
  - Validazione e auto-associazione
- Visualizzazione membri con avatar e badge creatore

### Availability & Booking Flow
**Flusso HOST**:
1. Utente crea DinnerAvailability con can_host=true per una data
2. Imposta max_guests
3. Status iniziale: AVAILABLE_TO_HOST
4. Altri membri prenotano → Observer aggiorna status automaticamente:
   - ALMOST_FULL (quasi pieno)
   - FULL (al completo)
5. Cron job daily imposta COMPLETED per date passate

**Flusso GUEST**:
1. Guest visualizza calendario disponibilità gruppo (GroupAvailabilities page)
2. Vede host con posti disponibili
3. Crea DinnerBooking specificando:
   - guests_count (include se stesso + accompagnatori)
   - bringing_items (cosa porta: vino, dolce, ecc.)
   - notes opzionali
4. Status PENDING → host conferma → CONFIRMED

**Validazioni automatiche** (Observers):
- Controllo capacità disponibile
- Aggiornamento status host
- Prevenzione overbooking
- Cancellazione prenotazioni se host cancella

### Calendar UI Features
**GroupAvailabilities Page** include:
- Toggle vista: Mensile / Settimanale
- Vista mensile: griglia 7 giorni (lun-dom) con navigazione mesi
- Vista settimanale: lista giornaliera con navigazione settimane
- Filtri per status e capacità host
- Prenotazione diretta da calendario
- Badge per prenotazioni utente
- Aggiornamento real-time disponibilità

### Scheduled Jobs Setup
Add to server crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
Schedule definito in `routes/console.php`:
- `availabilities:complete-expired` daily at 02:00 (Europe/Rome)

### Routes
Filament handles both panel routings. Add custom routes to [routes/web.php](routes/web.php) only when needed outside Filament panels.

### Environment
Copy `.env.example` to `.env` and configure:
- `APP_KEY` - Generated via `php artisan key:generate`
- `APP_LOCALE=it` - Consider setting Italian as default locale
- Reverb configuration for WebSocket (VITE_REVERB_* variables)
- Database connection (defaults to SQLite)
- Email configuration for email verification

### Testing Configuration
PHPUnit uses in-memory SQLite and array drivers for cache/session/queue to ensure test isolation. See [phpunit.xml](phpunit.xml).

### Development Workflow
When adding new features, follow this order:
1. Create/update migrations for database schema
2. Create/update Eloquent models with relationships
3. Create Filament resources for admin panel (if needed)
4. Create Filament resources/pages for user panel
5. Add policies for authorization
6. Update seeders for testing data
- non chiedere mai il permesso per usare il comando docker-compose dentro il progetto laravel

## Code Documentation Standards

### PHP DocBlocks
All classes, methods, and complex functions should have comprehensive PHPDoc comments following these guidelines:

#### Class-Level Documentation
Every class should have a DocBlock that includes:
- A one-line summary of the class purpose
- A detailed description explaining functionality and responsibilities
- Key features listed with bullet points
- `@see` tags linking to related classes/files
- Example:
```php
/**
 * Risorsa Filament per la gestione delle disponibilità degli utenti.
 *
 * Questa risorsa permette agli utenti di dichiarare la propria disponibilità
 * per una data specifica, indicando se possono ospitare (host) oppure se
 * vogliono partecipare come ospiti (guest).
 *
 * Funzionalità principali:
 * - Visualizza solo le disponibilità dell'utente autenticato
 * - Filtra le disponibilità per il gruppo dell'utente
 * - Gestisce stati automatici basati su prenotazioni (solo per host)
 *
 * @see DinnerAvailability Model principale
 * @see DinnerAvailabilityForm Form per creare/modificare disponibilità
 * @see DinnerAvailabilitiesTable Tabella con lista disponibilità
 */
class DinnerAvailabilityResource extends Resource
```

#### Method-Level Documentation
Every public and protected method should have a DocBlock that includes:
- A one-line summary of what the method does
- A detailed description of behavior and business logic
- `@param` tags for all parameters with type and description
- `@return` tag with return type and description
- `@throws` tags for exceptions (if applicable)
- `@see` tags for related methods or classes (if applicable)
- Example:
```php
/**
 * Configura la query Eloquent per questa risorsa.
 *
 * Filtra i record per mostrare solo:
 * - Le disponibilità dell'utente autenticato
 * - Le disponibilità del gruppo a cui appartiene l'utente
 *
 * Questo garantisce che ogni utente veda solo le proprie disponibilità
 * e non quelle di altri membri del gruppo.
 *
 * @return Builder Query Eloquent filtrata
 */
public static function getEloquentQuery(): Builder
```

#### Property Documentation
Class properties should have inline comments:
```php
/**
 * Modello Eloquent associato a questa risorsa.
 */
protected static ?string $model = DinnerAvailability::class;

/**
 * Label mostrata nel menu di navigazione.
 */
protected static ?string $navigationLabel = 'Disponibilità';
```

### Comment Language
- **Italian**: Use Italian for user-facing descriptions, business logic explanations
- **English**: Technical terms and framework-specific concepts can remain in English
- Be consistent within the same file/module

### When to Add Comments
1. **Always document**:
   - Public classes and interfaces
   - Public and protected methods
   - Complex algorithms or business logic
   - Non-obvious behavior or side effects
   - Configuration properties

2. **Don't over-document**:
   - Obvious getters/setters (unless they have side effects)
   - Self-explanatory code
   - Implementation details that are clear from the code itself

### Keeping Documentation Updated
- When modifying a method, **always update its DocBlock**
- When adding new functionality, add corresponding documentation
- Remove outdated comments during refactoring
- Claude Code should proactively suggest documentation updates when code changes

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.15
- filament/filament (FILAMENT) - v4
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/reverb (REVERB) - v1
- livewire/livewire (LIVEWIRE) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v3
- phpunit/phpunit (PHPUNIT) - v11
- laravel-echo (ECHO) - v2
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest
### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.
<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== filament/filament rules ===

## Filament
- Filament is used by this application, check how and where to follow existing application conventions.
- Filament is a Server-Driven UI (SDUI) framework for Laravel. It allows developers to define user interfaces in PHP using structured configuration objects. It is built on top of Livewire, Alpine.js, and Tailwind CSS.
- You can use the `search-docs` tool to get information from the official Filament documentation when needed. This is very useful for Artisan command arguments, specific code examples, testing functionality, relationship management, and ensuring you're following idiomatic practices.
- Utilize static `make()` methods for consistent component initialization.

### Artisan
- You must use the Filament specific Artisan commands to create new files or components for Filament. You can find these with the `list-artisan-commands` tool, or with `php artisan` and the `--help` option.
- Inspect the required options, always pass `--no-interaction`, and valid arguments for other options when applicable.

### Filament's Core Features
- Actions: Handle doing something within the application, often with a button or link. Actions encapsulate the UI, the interactive modal window, and the logic that should be executed when the modal window is submitted. They can be used anywhere in the UI and are commonly used to perform one-time actions like deleting a record, sending an email, or updating data in the database based on modal form input.
- Forms: Dynamic forms rendered within other features, such as resources, action modals, table filters, and more.
- Infolists: Read-only lists of data.
- Notifications: Flash notifications displayed to users within the application.
- Panels: The top-level container in Filament that can include all other features like pages, resources, forms, tables, notifications, actions, infolists, and widgets.
- Resources: Static classes that are used to build CRUD interfaces for Eloquent models. Typically live in `app/Filament/Resources`.
- Schemas: Represent components that define the structure and behavior of the UI, such as forms, tables, or lists.
- Tables: Interactive tables with filtering, sorting, pagination, and more.
- Widgets: Small component included within dashboards, often used for displaying data in charts, tables, or as a stat.

### Relationships
- Determine if you can use the `relationship()` method on form components when you need `options` for a select, checkbox, repeater, or when building a `Fieldset`:

<code-snippet name="Relationship example for Form Select" lang="php">
Forms\Components\Select::make('user_id')
    ->label('Author')
    ->relationship('author')
    ->required(),
</code-snippet>


## Testing
- It's important to test Filament functionality for user satisfaction.
- Ensure that you are authenticated to access the application within the test.
- Filament uses Livewire, so start assertions with `livewire()` or `Livewire::test()`.

### Example Tests

<code-snippet name="Filament Table Test" lang="php">
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
</code-snippet>

<code-snippet name="Filament Create Resource Test" lang="php">
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Howdy',
            'email' => 'howdy@example.com',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'Howdy',
        'email' => 'howdy@example.com',
    ]);
</code-snippet>

<code-snippet name="Testing Multiple Panels (setup())" lang="php">
    use Filament\Facades\Filament;

    Filament::setCurrentPanel('app');
</code-snippet>

<code-snippet name="Calling an Action in a Test" lang="php">
    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])->callAction('send');

    expect($invoice->refresh())->isSent()->toBeTrue();
</code-snippet>


### Important Version 4 Changes
- File visibility is now `private` by default.
- The `deferFilters` method from Filament v3 is now the default behavior in Filament v4, so users must click a button before the filters are applied to the table. To disable this behavior, you can use the `deferFilters(false)` method.
- The `Grid`, `Section`, and `Fieldset` layout components no longer span all columns by default.
- The `all` pagination page method is not available for tables by default.
- All action classes extend `Filament\Actions\Action`. No action classes exist in `Filament\Tables\Actions`.
- The `Form` & `Infolist` layout components have been moved to `Filament\Schemas\Components`, for example `Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.
- A new `Repeater` component for Forms has been added.
- Icons now use the `Filament\Support\Icons\Heroicon` Enum by default. Other options are available and documented.

### Organize Component Classes Structure
- Schema components: `Schemas/Components/`
- Table columns: `Tables/Columns/`
- Table filters: `Tables/Filters/`
- Actions: `Actions/`


=== tightenco/duster rules ===

## Duster Code Formatter

- You must run `vendor/bin/duster fix --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Duster wraps Laravel Pint and other formatters, so never run Pint directly. Always prefer Duster for formatting tasks.
</laravel-boost-guidelines>
