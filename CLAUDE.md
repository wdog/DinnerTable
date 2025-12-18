# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview: DinnerTable

**DinnerTable** is a team-based dinner scheduling application where users can form teams and coordinate weekly dinner rotations.

### Core Concept
Users register, join or create a team, and participate in weekly dinner scheduling. Each week, a cron job creates a new weekly calendar (Monday-Sunday) where team members can volunteer to host dinner by selecting:
- Day of the week
- Time slot
- Maximum number of guests

### User Roles & Panels
1. **Admin Panel** (`/admin`) - For administrators to manage the system
2. **User Panel** (`/`) - For end users to register, join teams, and manage dinner schedules

### Registration & Onboarding Flow
When a user registers, they must complete a Filament wizard with the following steps:
1. **Privacy & Contact**:
   - Accept privacy policy
   - First name (nome)
   - Last name (cognome)
   - Valid email with confirmation/verification
2. **Address & Hosting Details**:
   - Street and number (via e civico)
   - City (città)
   - Maximum guests they can host (numero massimo di ospiti)

### Team System
- Users can **create a team** and receive a unique team code
- Users can **join a team** using a team code
- Each user belongs to **only one team** at a time

### Weekly Calendar System
- **Automated Job**: Every Thursday at 9:00 AM, a cron job creates and enables the calendar for the following week (Monday-Sunday)
- Team members select availability to host dinner:
  - Day of the week
  - Time slot
  - Max number of guests for that event

### Future Development Notes
Additional features and associations will be specified in future iterations. This document will be updated as new requirements are defined.

## Development Commands

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

2. **User Panel** - End-user interface (default)
   - Path: `/` (root)
   - Resources in `app/Filament/User/Resources/`
   - Pages in `app/Filament/User/Pages/`
   - Widgets in `app/Filament/User/Widgets/`
   - For registered users to manage teams and dinner schedules

**Current Configuration**: [app/Providers/Filament/AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php)
- Currently only Admin panel exists
- User panel to be created with separate provider
- Both panels use FilamentShield for permissions

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

### Database Schema (Planned)

**Users Table** (Extended from Laravel default):
- Core fields: id, email, password, email_verified_at
- Profile fields: nome (first name), cognome (last name)
- Address: via_civico (street & number), citta (city)
- Hosting: max_ospiti (maximum guests user can host)
- Privacy: privacy_accepted_at (timestamp)
- Relations: belongs to one Team

**Teams Table**:
- id, name, team_code (unique)
- created_by (user_id)
- timestamps
- Relations: has many Users

**Weekly Calendars Table**:
- id, week_start_date, week_end_date
- enabled (boolean, set by cron job)
- team_id (foreign key)
- timestamps

**Dinner Events Table**:
- id, weekly_calendar_id
- host_user_id (foreign key to users)
- day_of_week (1-7 or date)
- time_slot
- max_guests
- timestamps

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

**Weekly Calendar Creation Job**:
- Runs every Thursday at 9:00 AM
- Creates WeeklyCalendar records for the following week (Monday-Sunday)
- Enables the calendar for team member access
- Command location: `app/Console/Commands/` (to be created)

## Project Structure

```
app/
├── Console/
│   └── Commands/           # Artisan commands
│       └── CreateWeeklyCalendars.php  # Thursday 9AM cron job (to be created)
├── Filament/
│   ├── Admin/              # Admin panel (/admin)
│   │   ├── Resources/      # Admin CRUD resources
│   │   ├── Pages/          # Admin custom pages
│   │   └── Widgets/        # Admin dashboard widgets
│   └── User/               # User panel (/) - to be created
│       ├── Resources/      # User resources
│       ├── Pages/          # User pages (team management, calendar)
│       └── Widgets/        # User dashboard widgets
├── Http/
│   └── Controllers/
├── Models/
│   ├── User.php           # Extended with profile, address, team relation
│   ├── Team.php           # To be created
│   ├── WeeklyCalendar.php # To be created
│   └── DinnerEvent.php    # To be created
├── Policies/              # Authorization policies (Shield integration)
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php  # Admin panel configuration
        └── UserPanelProvider.php   # User panel configuration (to be created)

database/
├── migrations/
│   ├── xxxx_create_users_table.php      # Extended with profile fields
│   ├── xxxx_create_teams_table.php      # To be created
│   ├── xxxx_create_weekly_calendars_table.php  # To be created
│   └── xxxx_create_dinner_events_table.php     # To be created
└── seeders/

resources/
├── css/
│   └── app.css       # Tailwind CSS entry
├── js/
│   ├── app.js        # Main JS entry
│   ├── bootstrap.js  # Axios & Laravel config
│   └── echo.js       # WebSocket/Reverb config
└── views/

routes/
├── web.php           # Custom web routes (if needed)
├── channels.php      # Broadcast channel authorization
└── console.php       # Artisan commands
```

## Important Notes

### Filament Multi-Panel Setup
- **Admin Panel**: Use `php artisan make:filament-resource ModelName --panel=admin`
- **User Panel**: Use `php artisan make:filament-resource ModelName --panel=user`
- Each panel has separate auto-discovery directories
- Panel access controlled via `canAccessPanel()` method in User model

### User Registration & Wizard
- New users must complete a Filament wizard during first login/registration
- Wizard steps:
  1. Privacy acceptance + personal info (nome, cognome, email verification)
  2. Address (via_civico, citta) + max_ospiti
- User cannot access main application until wizard is complete
- Consider using Filament's `getProfilePage()` or custom registration page

### Team Management
- Each user can belong to only one team at a time
- Team codes must be unique and easily shareable
- Consider using a short alphanumeric code generator (e.g., 6-8 characters)
- Switching teams should handle calendar/event cleanup

### Weekly Calendar Logic
- Calendars are team-specific
- Only enabled calendars can accept dinner event submissions
- Week boundaries: Monday 00:00 to Sunday 23:59
- Old calendars should be archived, not deleted (for history)

### Cron Job Setup
Add to server crontab or use Laravel Forge/Envoyer:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
Then define the Thursday 9 AM schedule in `app/Console/Kernel.php` or `routes/console.php`

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