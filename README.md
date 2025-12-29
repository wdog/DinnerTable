# 🍽️ DinnerTable

**DinnerTable** è un'applicazione web per la gestione e coordinamento di cene di gruppo settimanali. Permette agli utenti di organizzarsi in team, proporre di ospitare cene e partecipare agli eventi organizzati dai membri del gruppo.

## 📋 Indice

- [Funzionalità](#-funzionalità)
- [Stack Tecnologico](#-stack-tecnologico)
- [Prerequisiti](#-prerequisiti)
- [Installazione](#-installazione)
- [Configurazione](#-configurazione)
- [Sviluppo](#-sviluppo)
- [Struttura Progetto](#-struttura-progetto)
- [Credenziali di Test](#-credenziali-di-test)
- [Database](#-database)
- [Changelog](#-changelog)
- [Licenza](#-licenza)

## ✨ Funzionalità

### Per gli Utenti (Pannello App `/dinner`)
- ✅ Registrazione con verifica email obbligatoria
- ✅ Wizard completamento profilo obbligatorio con 3 step (dati personali, capacità hosting, privacy)
- ✅ Creazione e gestione gruppi cena con nome, slogan e immagine
- ✅ Codice invito univoco per ogni gruppo (14 caratteri alfanumerici)
- ✅ Partecipazione ai gruppi tramite codice invito
- ✅ Calendario disponibilità con vista mensile/settimanale interattivo
- ✅ Dichiarazione disponibilità come **HOST** (ospitare cena con numero max ospiti)
- ✅ Dichiarazione disponibilità come **GUEST** (partecipare o dichiarare assenza)
- ✅ Sistema prenotazioni: i guest prenotano presso host disponibili
- ✅ Gestione automatica stati (AVAILABLE → ALMOST_FULL → FULL → COMPLETED)
- ✅ Prenotazioni con numero ospiti, oggetti portati (vino, dolce, ecc.), note
- ✅ Visualizzazione membri del gruppo con avatar
- ✅ Completamento automatico disponibilità scadute (cron job daily)
- ✅ Tutorial integrato per guida uso applicazione
- ✅ Profilo utente modificabile con avatar

### Per gli Amministratori (Pannello Admin `/admin`)
- ✅ Gestione completa utenti con filtri e ricerca
- ✅ Visualizzazione stato verifica email
- ✅ Promozione/retrocessione amministratori
- ✅ Gestione gruppi cena con visualizzazione membri
- ✅ Tabella membri del gruppo (RelationManager)
- ✅ Statistiche e conteggi
- ✅ Accesso completo a tutti i dati del sistema

### Caratteristiche Tecniche
- 🎨 Home page pubblica responsive con presentazione progetto
- 🔐 Sistema di autenticazione a due pannelli (Admin/App)
- 📧 Verifica email obbligatoria con blocco accesso
- 🔒 Controllo accessi basato su ruoli (FilamentShield)
- 🌍 Interfaccia completamente in italiano
- 🔄 Aggiornamenti real-time con Laravel Echo e Reverb
- 🎯 Sistema stati complesso con 7 stati disponibilità + 3 stati prenotazioni
- 🤖 Automazioni con Observer pattern per business logic
- 🔐 Policy granulari per autorizzazioni (create, update, delete, book)
- ⏰ Scheduled jobs per completamento automatico cene passate
- 📊 Validazioni automatiche capacità e prevenzione overbooking

## 🛠️ Stack Tecnologico

### Backend
- **Laravel 12.x** - Framework PHP con struttura streamlined
- **Filament v4** - Admin panel con architettura multi-panel (Admin + App)
- **FilamentShield** - Gestione ruoli e permessi
- **Spatie Laravel Permission** - Sistema di permessi
- **Observer Pattern** - Business logic automatica (stati, validazioni)
- **Policy-based Authorization** - Controllo accessi granulare

### Frontend
- **Tailwind CSS v4** - Framework CSS con @theme directive
- **Vite** - Build tool con HMR
- **Laravel Echo** - WebSocket client
- **Pusher JS** - Real-time communication
- **Alpine.js** - Interattività UI (incluso con Livewire)
- **Livewire v3** - Componenti dinamici server-side

### Database & Cache
- **SQLite** - Database principale (development)
- **MySQL** - Database principale (production ready)
- **Laravel Reverb** - WebSocket server per real-time updates

### DevOps
- **Docker & Docker Compose** - Containerizzazione
- **PHP 8.3+**
- **Node.js & NPM**

## 📦 Prerequisiti

- Docker & Docker Compose
- Git

## 🚀 Installazione

### 1. Clona il repository
```bash
git clone <repository-url>
cd DinnerTable/src
```

### 2. Setup completo automatico
```bash
docker-compose up -d
docker-compose exec app composer setup
```

Questo comando eseguirà automaticamente:
- `composer install` - Installazione dipendenze PHP
- Creazione file `.env` da `.env.example`
- `php artisan key:generate` - Generazione chiave applicazione
- `php artisan migrate` - Esecuzione migrations
- `npm install` - Installazione dipendenze Node
- `npm run build` - Build assets di produzione

### 3. Crea utente amministratore
```bash
docker-compose exec app php artisan tinker --execute="
App\Models\User::updateOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin',
        'password' => Hash::make('password'),
        'is_admin' => true,
        'email_verified_at' => now()
    ]
);"
```

## ⚙️ Configurazione

### File .env

Configurazioni principali da verificare:

```env
APP_NAME="DinnerTable"
APP_ENV=local
APP_URL=https://localhost
APP_LOCALE=it

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

MAIL_MAILER=log
```

### Configurazione Email

Durante lo sviluppo, le email vengono salvate nei log:
```bash
docker-compose exec app php artisan pail
```

Per produzione, configura un servizio SMTP nel file `.env`.

## 💻 Sviluppo

### Avvio ambiente di sviluppo
```bash
docker-compose exec app composer dev
```

Questo avvia in parallelo:
- Server PHP (`php artisan serve`)
- Queue listener
- Pail logs
- Vite dev server con HMR

### Comandi individuali

```bash
# Server PHP
docker-compose exec app php artisan serve

# Queue worker
docker-compose exec app php artisan queue:listen --tries=1

# Logs in tempo reale
docker-compose exec app php artisan pail --timeout=0

# Vite dev server
npm run dev
```

### Build produzione
```bash
npm run build
```

### Database

```bash
# Esegui migrations
docker-compose exec app php artisan migrate

# Fresh database con seeding
docker-compose exec app php artisan migrate:fresh --seed

# Rollback
docker-compose exec app php artisan migrate:rollback
```

### Code Quality

```bash
# Laravel Pint (code formatting)
docker-compose exec app vendor/bin/pint

# Tighten Duster (linting)
docker-compose exec app vendor/bin/duster
```

### Testing

```bash
# Tutti i test
docker-compose exec app composer test

# Test specifico
docker-compose exec app php artisan test --filter=TestName
```

## 📁 Struttura Progetto

```
src/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CompleteExpiredAvailabilities.php  # Cron job daily 02:00
│   ├── Enums/
│   │   ├── DinnerAvailabilityStatus.php  # 7 stati disponibilità
│   │   ├── DinnerBookingStatus.php       # 3 stati prenotazione
│   │   └── CancellationReason.php        # Motivi cancellazione
│   ├── Filament/
│   │   ├── Admin/             # Pannello Admin (/admin)
│   │   │   ├── Resources/
│   │   │   │   ├── UserResource.php
│   │   │   │   └── DinnerGroupResource/  # Con MembersRelationManager
│   │   │   ├── Pages/
│   │   │   └── Widgets/
│   │   └── App/               # Pannello App (/dinner)
│   │       ├── Resources/
│   │       │   ├── DinnerAvailabilityResource/  # Disponibilità host/guest
│   │       │   └── DinnerBookingResource/       # Prenotazioni
│   │       ├── Pages/
│   │       │   ├── CompleteProfile.php          # Wizard profilo
│   │       │   ├── ManageDinnerGroup.php        # Gestione gruppo
│   │       │   ├── GroupAvailabilities.php      # Calendario mensile/settimanale
│   │       │   └── TutorialPage.php
│   │       └── Widgets/
│   ├── Http/
│   │   └── Middleware/
│   │       └── EnsureProfileIsComplete.php  # Middleware profilo obbligatorio
│   ├── Models/
│   │   ├── User.php              # Base + is_admin + dinner_group_id
│   │   ├── Profile.php           # Profilo separato 1:1
│   │   ├── DinnerGroup.php       # Gruppo cena
│   │   ├── DinnerDate.php        # Date individuali
│   │   ├── DinnerAvailability.php # Disponibilità host/guest
│   │   └── DinnerBooking.php      # Prenotazioni
│   ├── Observers/
│   │   ├── DinnerAvailabilityObserver.php  # Auto-update stati
│   │   └── DinnerBookingObserver.php       # Validazioni capacità
│   ├── Policies/
│   │   ├── DinnerAvailabilityPolicy.php
│   │   └── DinnerBookingPolicy.php
│   └── Providers/
│       └── Filament/
│           ├── AdminPanelProvider.php  # /admin
│           └── AppPanelProvider.php    # /dinner
├── database/
│   ├── factories/
│   ├── migrations/            # 6 tabelle principali + permission
│   └── seeders/               # User, DinnerGroup, DinnerDate seeders
├── resources/
│   ├── css/
│   │   └── app.css           # Tailwind CSS v4
│   ├── js/
│   │   ├── app.js            # JavaScript principale
│   │   └── echo.js           # Laravel Echo + Reverb config
│   └── views/
│       ├── home.blade.php    # Home page pubblica
│       └── components/
│           └── view-toggle.blade.php  # Toggle vista calendario
├── routes/
│   ├── web.php               # Route home pubblica
│   ├── channels.php          # Broadcast channels
│   └── console.php           # Scheduled commands
└── docker-compose.yml        # Configurazione Docker
```

## 🔐 Credenziali di Test

### Amministratore
- **Email**: `admin@example.com`
- **Password**: `password`
- **Accesso**: Pannello Admin (`/admin`) e App (`/dinner`)
- **Note**: Profilo già completato, può accedere direttamente

### Utente Test
- **Email**: Creato durante registrazione
- **Password**: Scelta durante registrazione
- **Accesso**: Solo pannello App (`/dinner`)
- **Note**: Deve completare wizard profilo al primo accesso

## 🗄️ Database

### Schema Principale

**users** (Laravel base + extensions)
```sql
- id, name, email, password, email_verified_at
- is_admin (boolean, default: false)
- dinner_group_id (FK nullable a dinner_groups)
- timestamps
```

**profiles** (1:1 con User)
```sql
- id, user_id (FK unique)
- city, address, house_number, postal_code
- max_guests (integer)
- privacy_accepted_at (timestamp)
- avatar_url (nullable)
- timestamps
```

**dinner_groups**
```sql
- id, name, slogan (nullable), group_image (nullable)
- group_code (14 chars alfanumerici, unique)
- created_by (FK users)
- timestamps
```

**dinner_dates**
```sql
- id, dinner_group_id (FK), dinner_date (date)
- timestamps
- UNIQUE(dinner_group_id, dinner_date)
```

**dinner_availabilities**
```sql
- id, dinner_date_id (FK), user_id (FK)
- status (enum: 7 possibili valori)
- can_host (boolean)
- max_guests (integer nullable)
- note (text nullable)
- cancellation_reason (enum nullable)
- timestamps
- UNIQUE(dinner_date_id, user_id)
```

**dinner_bookings**
```sql
- id, host_availability_id (FK), guest_user_id (FK)
- guests_count (integer)
- bringing_items (json array)
- notes (text nullable)
- status (enum: pending/confirmed/cancelled)
- timestamps
- UNIQUE(host_availability_id, guest_user_id)
```

### Stati (Enums)

**DinnerAvailabilityStatus** (7 stati):
- `AVAILABLE_TO_HOST` - Host con posti disponibili
- `ALMOST_FULL` - Host quasi al completo
- `FULL` - Host al completo
- `HOST_CANCELLED` - Cancellato dall'host
- `COMPLETED` - Cena completata
- `AVAILABLE` - Guest disponibile
- `NOT_AVAILABLE` - Guest non disponibile

**DinnerBookingStatus** (3 stati):
- `PENDING` - In attesa conferma
- `CONFIRMED` - Confermato
- `CANCELLED` - Cancellato

### Relazioni Principali
- `User` hasOne `Profile`
- `User` belongsTo `DinnerGroup`
- `User` hasMany `DinnerAvailability`
- `User` hasMany `DinnerBooking` (come guest)
- `DinnerGroup` hasMany `User` (members)
- `DinnerGroup` belongsTo `User` (creator)
- `DinnerGroup` hasMany `DinnerDate`
- `DinnerDate` hasMany `DinnerAvailability`
- `DinnerAvailability` hasMany `DinnerBooking` (quando è host)

## 🌐 Routes

### Pubbliche
- `/` - Home page pubblica con presentazione progetto

### Pannello App (Utenti)
- `/dinner` - Login utenti
- `/dinner/register` - Registrazione nuovi utenti
- `/dinner/complete-profile` - Wizard completamento profilo obbligatorio
- `/dinner/manage-dinner-group` - Creazione/join gruppo cena
- `/dinner/group-availabilities` - Calendario disponibilità (mensile/settimanale)
- `/dinner/dinner-availabilities` - Gestione proprie disponibilità
- `/dinner/dinner-bookings` - Gestione proprie prenotazioni
- `/dinner/tutorial` - Tutorial uso applicazione
- `/dinner/profile` - Modifica profilo utente

### Pannello Admin (Amministratori)
- `/admin` - Login amministratori
- `/admin/users` - Gestione utenti (CRUD completo)
- `/admin/dinner-groups` - Gestione gruppi cena con membri

### Scheduled Commands
- `availabilities:complete-expired` - Daily at 02:00 (Europe/Rome)

## 📝 Changelog

Vedi [CHANGELOG.md](CHANGELOG.md) per la lista completa delle modifiche.

## 🤝 Contribuire

Le pull request sono benvenute! Per modifiche importanti, apri prima un issue per discutere cosa vorresti cambiare.

## 📄 Licenza

Questo progetto è rilasciato sotto licenza MIT. Vedi il file `LICENSE` per maggiori dettagli.

---

**Sviluppato con** ❤️ **usando Laravel + Filament**
