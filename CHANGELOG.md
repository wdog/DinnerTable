# Changelog

Tutte le modifiche rilevanti al progetto DinnerTable saranno documentate in questo file.

## [Non rilasciato] - 2025-12-19

### Aggiunto
- **Home page pubblica** (`/`)
  - Design moderno con Tailwind CSS
  - Sezioni: Hero, Come funziona, CTA
  - Footer a tre colonne con link ai pannelli
  - Responsive design

- **Sistema a due pannelli Filament**
  - **Pannello Friend** (`/friend`) - Per utenti normali
    - Login e registrazione
    - Verifica email obbligatoria
    - Pannello default
    - Colore primario: Purple
  - **Pannello Admin** (`/admin`) - Per amministratori
    - Accesso limitato agli utenti con flag `is_admin`
    - Gestione utenti e gruppi cena
    - Colore primario: Lime

- **Modello DinnerGroup (Gruppi Cena)**
  - Campo `name` - Nome del gruppo
  - Campo `slogan` - Slogan del gruppo
  - Campo `image` - Immagine del gruppo
  - Campo `group_code` - Codice invito univoco (32 caratteri alfanumerici)
  - Campo `created_by` - Utente creatore del gruppo
  - Relazioni: `creator()` e `members()`

- **Campo is_admin per Users**
  - Flag booleano per identificare amministratori
  - Controllo accesso al pannello admin
  - Visibile e modificabile nel pannello admin

- **Risorsa Users (Pannello Admin)**
  - Lista utenti con colonne: Nome, Email, Verificato, Admin, Gruppo Cena
  - Icone per stato verifica e admin
  - Form con toggle per promuovere/retrocedere admin
  - Ricerca e ordinamento

- **Risorsa DinnerGroups (Pannello Admin)**
  - Lista gruppi con: Nome, Slogan, Codice Invito, Creatore, Conteggio membri
  - Codice invito copiabile con font monospace
  - Conteggio membri con relazione

### Modificato
- **Modello User**
  - Aggiunta relazione `dinnerGroup()` con BelongsTo
  - Campo `dinner_group_id` per appartenenza al gruppo
  - Implementazione `MustVerifyEmail` per verifica email
  - Metodo `canAccessPanel()` personalizzato per controllo accessi
  - Cast automatici per `email_verified_at`, `password`, `is_admin`

- **Struttura directory Filament**
  - Risorse spostate in sottocartelle per pannello:
    - `app/Filament/Admin/Resources/`
    - `app/Filament/Friend/Resources/`
  - Namespace aggiornati di conseguenza

### Configurazione
- **Database**: MySQL (Docker)
- **Email**: Driver `log` per sviluppo
- **Locale**: Italiano (`it`)
- **Autenticazione**: Verifica email obbligatoria per pannello Friend

### Struttura Database
```
users
├── id
├── name
├── email
├── password
├── email_verified_at
├── is_admin (boolean, default: false)
├── dinner_group_id (nullable, FK)
├── created_at
└── updated_at

dinner_groups
├── id
├── name
├── slogan (nullable)
├── image (nullable)
├── group_code (32 chars, unique)
├── created_by (FK users)
├── created_at
└── updated_at
```

### Routes
- `/` - Home page pubblica
- `/friend` - Pannello utenti (login/registrazione)
- `/admin` - Pannello amministratori

### Credenziali di Test
**Admin:**
- Email: `admin@example.com`
- Password: `password`
- Accesso: Pannello Admin e Friend

### Note Tecniche
- Utilizzo di Filament v4 con architettura multi-panel
- FilamentShield per gestione permessi
- Laravel Reverb configurato per WebSocket
- Vite con HMR per sviluppo frontend
