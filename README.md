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

### Per gli Utenti (Pannello Friend)
- ✅ Registrazione con verifica email obbligatoria
- ✅ Creazione e gestione gruppi cena
- ✅ Codice invito univoco per ogni gruppo (32 caratteri alfanumerici)
- ✅ Partecipazione ai gruppi tramite codice invito
- 🔜 Calendario settimanale delle cene (Monday-Sunday)
- 🔜 Proposta per ospitare cene con orario e numero massimo ospiti
- 🔜 Visualizzazione eventi del proprio gruppo

### Per gli Amministratori (Pannello Admin)
- ✅ Gestione completa utenti
- ✅ Visualizzazione stato verifica email
- ✅ Promozione/retrocessione amministratori
- ✅ Gestione gruppi cena
- ✅ Visualizzazione membri di ogni gruppo
- ✅ Statistiche e conteggi

### Caratteristiche Tecniche
- 🎨 Home page pubblica responsive
- 🔐 Sistema di autenticazione a due pannelli (Admin/Friend)
- 📧 Verifica email obbligatoria
- 🔒 Controllo accessi basato su ruoli
- 🌍 Interfaccia completamente in italiano

## 🛠️ Stack Tecnologico

### Backend
- **Laravel 11.x** - Framework PHP
- **Filament v4** - Admin panel con architettura multi-panel
- **FilamentShield** - Gestione ruoli e permessi
- **Spatie Laravel Permission** - Sistema di permessi

### Frontend
- **Tailwind CSS v4** - Framework CSS
- **Vite** - Build tool con HMR
- **Laravel Echo** - WebSocket client
- **Pusher JS** - Real-time communication

### Database & Cache
- **MySQL** - Database principale
- **Laravel Reverb** - WebSocket server

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
│   │   └── Commands/          # Comandi Artisan custom
│   ├── Filament/
│   │   ├── Admin/             # Pannello Amministratore
│   │   │   ├── Resources/
│   │   │   │   ├── Users/     # Gestione utenti
│   │   │   │   └── DinnerGroups/  # Gestione gruppi
│   │   │   ├── Pages/
│   │   │   └── Widgets/
│   │   └── Friend/            # Pannello Utenti
│   │       ├── Resources/
│   │       ├── Pages/
│   │       └── Widgets/
│   ├── Models/
│   │   ├── User.php           # Utente con verifica email
│   │   └── DinnerGroup.php    # Gruppo cena
│   └── Providers/
│       └── Filament/
│           ├── AdminPanelProvider.php
│           └── FriendPanelProvider.php
├── database/
│   └── migrations/            # Schema database
├── resources/
│   ├── css/
│   │   └── app.css           # Tailwind CSS
│   ├── js/
│   │   ├── app.js            # JavaScript principale
│   │   └── echo.js           # Laravel Echo config
│   └── views/
│       └── home.blade.php    # Home page pubblica
├── routes/
│   ├── web.php               # Routes web
│   └── channels.php          # Broadcast channels
└── docker-compose.yml        # Configurazione Docker
```

## 🔐 Credenziali di Test

### Amministratore
- **Email**: `admin@example.com`
- **Password**: `password`
- **Accesso**: Pannello Admin (`/admin`) e Friend (`/friend`)

### Utente Test
- **Email**: `test@example.com`
- **Password**: Creato durante registrazione
- **Accesso**: Solo pannello Friend (`/friend`)

## 🗄️ Database

### Schema Principale

**users**
```sql
- id
- name
- email
- password
- email_verified_at
- is_admin (boolean, default: false)
- dinner_group_id (FK nullable)
- created_at, updated_at
```

**dinner_groups**
```sql
- id
- name
- slogan (nullable)
- image (nullable)
- group_code (32 chars, unique)
- created_by (FK users)
- created_at, updated_at
```

### Relazioni
- `User` belongsTo `DinnerGroup`
- `DinnerGroup` hasMany `Users` (members)
- `DinnerGroup` belongsTo `User` (creator)

## 🌐 Routes

### Pubbliche
- `/` - Home page

### Pannello Friend (Utenti)
- `/friend` - Login
- `/friend/register` - Registrazione
- `/friend/*` - Dashboard e funzionalità utente

### Pannello Admin (Amministratori)
- `/admin` - Login admin
- `/admin/users` - Gestione utenti
- `/admin/dinner-groups` - Gestione gruppi cena

## 📝 Changelog

Vedi [CHANGELOG.md](CHANGELOG.md) per la lista completa delle modifiche.

## 🤝 Contribuire

Le pull request sono benvenute! Per modifiche importanti, apri prima un issue per discutere cosa vorresti cambiare.

## 📄 Licenza

Questo progetto è rilasciato sotto licenza MIT. Vedi il file `LICENSE` per maggiori dettagli.

---

**Sviluppato con** ❤️ **usando Laravel + Filament**
