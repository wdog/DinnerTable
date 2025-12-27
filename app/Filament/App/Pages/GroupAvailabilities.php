<?php

namespace App\Filament\App\Pages;

use UnitEnum;
use Exception;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use App\Models\DinnerDate;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use App\Models\DinnerBooking;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use App\Policies\DinnerBookingPolicy;
use App\Rules\ValidateBookingCapacity;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

/**
 * Pagina per visualizzare tutte le disponibilità dei membri del gruppo cena.
 *
 * Questa pagina permette agli utenti di visualizzare un calendario mensile
 * con tutte le disponibilità dichiarate dai membri del proprio gruppo.
 * Include funzionalità di navigazione tra i mesi, filtri per stato e capacità
 * di ospitare, e la possibilità di prenotare direttamente dalle disponibilità.
 *
 * Funzionalità principali:
 * - Visualizzazione calendario mensile con griglia 7 giorni (lun-dom)
 * - Navigazione tra mesi (precedente/successivo)
 * - Filtri per stato disponibilità e capacità di ospitare
 * - Prenotazione diretta tramite modal interattivo
 * - Controllo autorizzazioni tramite Policy
 * - Aggiornamento real-time tramite eventi Livewire
 *
 * @see DinnerAvailability Modello delle disponibilità
 * @see DinnerBooking Modello delle prenotazioni
 * @see DinnerBookingPolicy Policy per autorizzazioni prenotazioni
 * @see DinnerDate Modello delle date cena
 */
class GroupAvailabilities extends Page implements HasActions
{
    use InteractsWithActions;

    /**
     * Icona mostrata nel menu di navigazione (Tabler Icons).
     */
    protected static string|BackedEnum|null $navigationIcon = 'tabler-calendar-user';

    /**
     * Label mostrata nel menu di navigazione.
     */
    protected static ?string $navigationLabel = 'Calendario Disponibilità';

    /**
     * Gruppo di navigazione nel menu laterale.
     */
    protected static string|UnitEnum|null $navigationGroup = 'Gestione Cene';

    /**
     * Ordine di visualizzazione nel menu di navigazione.
     */
    protected static ?int $navigationSort = 1;

    /**
     * ID della disponibilità selezionata per la prenotazione.
     *
     * Viene popolato quando l'utente clicca su "Prenota" e utilizzato
     * nel modal di creazione prenotazione.
     */
    public ?string $bookingAvailabilityId = null;

    /**
     * Dati temporanei del form di prenotazione.
     *
     * Array che contiene i dati inseriti dall'utente nel form di prenotazione
     * prima della conferma (guests_count, bringing_items, notes).
     */
    public array $bookingData = [];

    /**
     * Mese e anno selezionati (formato Y-m).
     *
     * Utilizzato per filtrare il calendario. Viene inizializzato al mese
     * corrente nel metodo mount() e può essere modificato dall'utente.
     */
    public ?string $selectedMonth = null;

    /**
     * Filtro per status disponibilità.
     *
     * Permette di filtrare le disponibilità mostrate nel calendario
     * per uno specifico status (Available, Maybe, Unavailable).
     */
    public ?string $filterStatus = null;

    /**
     * Filtro per capacità di ospitare.
     *
     * Se true, mostra solo le disponibilità di utenti che possono ospitare (can_host = true).
     */
    public bool $filterCanHost = false;

    /**
     * Dati del calendario per il mese corrente.
     *
     * Array che contiene la struttura del calendario mensile con:
     * - celle vuote per allineamento inizio settimana
     * - giorni del mese con relative disponibilità filtrate
     * - informazioni aggregate per ogni giorno
     */
    public array $calendarData = [];

    /**
     * Path della vista blade che renderizza questa pagina.
     */
    protected string $view = 'filament.app.pages.group-availabilities';

    /**
     * Verifica se l'utente può accedere alla pagina.
     *
     * Solo gli utenti che appartengono a un gruppo cena possono accedere.
     *
     * @return bool True se l'utente può accedere
     */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->dinner_group_id !== null;
    }

    /**
     * Ottiene il titolo dinamico della pagina con il nome del gruppo.
     *
     * Il titolo viene generato includendo il nome del gruppo cena dell'utente
     * autenticato, se disponibile. Altrimenti mostra un titolo generico.
     *
     * @return string Titolo della pagina (es. "Disponibilità - Gruppo A")
     */
    public function getTitle(): string
    {
        $groupName = Auth::user()->dinnerGroup?->name;

        return $groupName
            ? "Disponibilità - {$groupName}"
            : 'Disponibilità del Gruppo';
    }

    /**
     * Inizializza il componente Livewire.
     *
     * Imposta il mese corrente come default e carica i dati del calendario.
     * Questo metodo viene chiamato automaticamente quando il componente viene montato.
     */
    public function mount(): void
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Naviga al mese precedente nel calendario.
     *
     * Decrementa il mese selezionato di uno e ricarica i dati del calendario.
     * Gestisce correttamente il passaggio da gennaio a dicembre dell'anno precedente.
     */
    public function previousMonth(): void
    {
        [$year, $month]      = explode('-', $this->selectedMonth);
        $date                = Carbon::create($year, $month, 1)->subMonth();
        $this->selectedMonth = $date->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Naviga al mese successivo nel calendario.
     *
     * Incrementa il mese selezionato di uno e ricarica i dati del calendario.
     * Gestisce correttamente il passaggio da dicembre a gennaio dell'anno successivo.
     */
    public function nextMonth(): void
    {
        [$year, $month]      = explode('-', $this->selectedMonth);
        $date                = Carbon::create($year, $month, 1)->addMonth();
        $this->selectedMonth = $date->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Livewire lifecycle hook: eseguito quando selectedMonth viene modificato.
     *
     * Ricarica automaticamente i dati del calendario quando l'utente
     * seleziona un mese diverso dal dropdown.
     */
    public function updatedSelectedMonth(): void
    {
        $this->loadCalendarData();
    }

    /**
     * Livewire lifecycle hook: eseguito quando filterStatus viene modificato.
     *
     * Ricarica i dati del calendario applicando il nuovo filtro per status.
     */
    public function updatedFilterStatus(): void
    {
        $this->loadCalendarData();
    }

    /**
     * Livewire lifecycle hook: eseguito quando filterCanHost viene modificato.
     *
     * Ricarica i dati del calendario applicando il filtro per capacità di ospitare.
     */
    public function updatedFilterCanHost(): void
    {
        $this->loadCalendarData();
    }

    /**
     * Ottiene il nome formattato del mese corrente.
     *
     * @return string Nome del mese e anno in italiano (es. "Dicembre 2025")
     */
    public function getMonthName(): string
    {
        if ( ! $this->selectedMonth) {
            return '';
        }

        [$year, $month] = explode('-', $this->selectedMonth);

        return Carbon::create($year, $month, 1)->isoFormat('MMMM YYYY');
    }

    /**
     * Genera le opzioni per il selettore del mese.
     *
     * Crea un array di opzioni per il dropdown di selezione mese,
     * includendo i 2 mesi precedenti e i 12 mesi successivi al mese corrente.
     * I mesi sono formattati in italiano (es. "Gennaio 2025").
     *
     * @return array Array associativo [Y-m => "Mese Anno"]
     */
    public function getMonthOptions(): array
    {
        $options = [];
        $start   = Carbon::now()->subMonths(2);
        $end     = Carbon::now()->addMonths(12);

        for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
            $key           = $date->format('Y-m');
            $options[$key] = $date->isoFormat('MMMM YYYY');
        }

        return $options;
    }

    /**
     * Genera le opzioni per il filtro status.
     *
     * Crea un array raggruppato con tutti gli stati disponibili dall'enum
     * DinnerAvailabilityStatus, separando gli stati HOST da quelli GUEST.
     *
     * @return array Array associativo con optgroups ['Host' => [...], 'Guest' => [...]]
     */
    public function getStatusFilterOptions(): array
    {
        $hostStates  = [];
        $guestStates = [];

        foreach (\App\Enums\DinnerAvailabilityStatus::cases() as $status) {
            if ($status->isHostStatus()) {
                $hostStates[$status->value] = $status->getLabel();
            } elseif ($status->isGuestStatus()) {
                $guestStates[$status->value] = $status->getLabel();
            }
        }

        return [
            'Host (chi cucina)'     => $hostStates,
            'Guest (chi partecipa)' => $guestStates,
        ];
    }

    /**
     * Carica i dati del calendario per il mese selezionato.
     *
     * Recupera tutte le DinnerDate e disponibilità del gruppo per il mese corrente,
     * applica i filtri attivi (status e can_host) e costruisce una griglia calendario
     * di 7 colonne (lunedì-domenica) con celle vuote per l'allineamento.
     *
     * Il metodo viene invocato automaticamente:
     * - Al mount del componente
     * - Quando cambia il mese selezionato
     * - Quando cambiano i filtri
     * - Quando viene emesso l'evento Livewire 'data-updated'
     *
     * Per ogni giorno del mese, calcola:
     * - Numero totale di disponibilità (filtrate)
     * - Numero di disponibilità con can_host = true
     * - Dettagli di ogni disponibilità (utente, status, posti, prenotabilità)
     */
    #[On('data-updated')]
    public function loadCalendarData(): void
    {
        if ( ! $this->selectedMonth) {
            $this->calendarData = [];

            return;
        }

        [$year, $month] = explode('-', $this->selectedMonth);

        // Recupera tutte le date del gruppo per il mese selezionato
        $dates = DinnerDate::where('dinner_group_id', Auth::user()->dinner_group_id)
            ->whereYear('dinner_date', $year)
            ->whereMonth('dinner_date', $month)
            ->with(['availabilities.user'])
            ->orderBy('dinner_date')
            ->get()
            ->keyBy(function ($date) {
                return Carbon::parse($date->dinner_date)->day;
            });

        // Calcola il primo giorno del mese e quanti giorni ha il mese
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $daysInMonth     = $firstDayOfMonth->daysInMonth;

        // Calcola l'offset per iniziare da lunedì (1 = lunedì, 7 = domenica)
        $dayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1 (lunedì) a 7 (domenica)
        $offset    = $dayOfWeek - 1; // 0 per lunedì, 6 per domenica

        // Costruisce l'array del calendario con celle vuote all'inizio
        $calendar = [];

        // Aggiungi celle vuote prima del primo giorno
        for ($i = 0; $i < $offset; $i++) {
            $calendar[] = ['empty' => true];
        }

        // Aggiungi tutti i giorni del mese
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dinnerDate = $dates->get($day);

            if ($dinnerDate) {
                $availabilities = $dinnerDate->availabilities;

                // Applica i filtri alle disponibilità
                $filteredAvailabilities = $availabilities->filter(function ($availability) {
                    // Filtro per status
                    if ($this->filterStatus && $availability->status->value !== $this->filterStatus) {
                        return false;
                    }

                    // Filtro per can_host
                    if ($this->filterCanHost && ! $availability->can_host) {
                        return false;
                    }

                    return true;
                });

                // Verifica se l'utente ha prenotazioni per questa data
                $userBooking = DinnerBooking::where('guest_user_id', Auth::id())
                    ->whereHas('hostAvailability.dinnerDate', function ($query) use ($dinnerDate) {
                        $query->where('id', $dinnerDate->id);
                    })
                    ->with('hostAvailability.user')
                    ->first();

                $calendar[] = [
                    'empty'                => false,
                    'date'                 => $dinnerDate->dinner_date,
                    'day'                  => $day,
                    'day_name'             => Carbon::parse($dinnerDate->dinner_date)->isoFormat('ddd'),
                    'total_availabilities' => $filteredAvailabilities->count(),
                    'can_host_count'       => $filteredAvailabilities->where('can_host', true)->count(),
                    'user_booking'         => $userBooking ? [
                        'id'        => $userBooking->id,
                        'status'    => $userBooking->status,
                        'host_name' => $userBooking->hostAvailability->user->name,
                    ] : null,
                    'availabilities' => $filteredAvailabilities->map(function ($availability) {
                        $canBook = $this->canBook($availability->id);

                        return [
                            'id'              => $availability->id,
                            'user_name'       => $availability->user->name,
                            'status'          => $availability->status,
                            'can_host'        => $availability->can_host,
                            'note'            => $availability->note,
                            'can_book'        => $canBook,
                            'max_guests'      => $availability->max_guests ?? 0,
                            'available_spots' => $availability->available_spots ?? 0,
                            'total_booked'    => $availability->total_booked_guests ?? 0,
                        ];
                    })->toArray(),
                ];
            } else {
                // Giorno senza dati nel database
                $currentDate = Carbon::create($year, $month, $day);
                $calendar[]  = [
                    'empty'                => false,
                    'date'                 => $currentDate,
                    'day'                  => $day,
                    'day_name'             => $currentDate->isoFormat('ddd'),
                    'total_availabilities' => 0,
                    'available_count'      => 0,
                    'maybe_count'          => 0,
                    'unavailable_count'    => 0,
                    'can_host_count'       => 0,
                    'user_booking'         => null,
                    'availabilities'       => [],
                ];
            }
        }

        $this->calendarData = $calendar;
    }

    /**
     * Verifica se l'utente corrente può prenotare una specifica disponibilità.
     *
     * Utilizza la DinnerBookingPolicy per determinare se l'utente ha i permessi
     * necessari per creare una prenotazione sulla disponibilità specificata.
     * Le verifiche includono:
     * - L'utente non può prenotare se stesso
     * - Deve esserci disponibilità di posti
     * - La disponibilità deve essere prenotabile (can_host = true)
     *
     * @param  int  $availabilityId  ID della disponibilità da verificare
     * @return bool True se l'utente può prenotare, false altrimenti
     */
    public function canBook(int $availabilityId): bool
    {
        $availability = DinnerAvailability::find($availabilityId);
        if ( ! $availability) {
            return false;
        }

        $policy = app(DinnerBookingPolicy::class);

        return $policy->book(Auth::user(), $availability);
    }

    /**
     * Apre il modal di prenotazione per una specifica disponibilità.
     *
     * Imposta l'ID della disponibilità selezionata e monta l'action Filament
     * che gestisce il modal di creazione prenotazione.
     *
     * @param  int  $availabilityId  ID della disponibilità da prenotare
     */
    public function openBookingModal(int $availabilityId): void
    {
        $this->bookingAvailabilityId = $availabilityId;
        $this->mountAction('createBooking');
    }

    /**
     * Action Filament per creare una nuova prenotazione.
     *
     * Crea e configura l'action Filament che gestisce il modal di prenotazione.
     * Il modal include:
     * - Heading dinamico con nome host, data e posti disponibili
     * - Form con campi: numero ospiti, cosa si porta, note/allergie
     * - Validazione capacità tramite ValidateBookingCapacity rule
     * - Verifiche autorizzazioni tramite Policy
     * - Prevenzione prenotazioni duplicate
     * - Notifiche di successo/errore
     * - Aggiornamento automatico calendario dopo prenotazione
     *
     * L'Observer DinnerBookingObserver gestirà automaticamente:
     * - Cambio stato disponibilità host (se raggiunge max_guests)
     * - Cambio stato disponibilità guest a Booked
     * - Invio notifiche ai partecipanti
     *
     * @return Action Action Filament configurato per la creazione prenotazione
     *
     * @see ValidateBookingCapacity Rule per validazione capacità
     * @see DinnerBookingPolicy Policy per autorizzazioni
     * @see DinnerBookingObserver Observer per side effects
     */
    public function createBooking(): Action
    {
        return Action::make('createBooking')
            ->modalHeading(function () {
                $availability = DinnerAvailability::with(['user', 'dinnerDate'])->find($this->bookingAvailabilityId);

                if ( ! $availability) {
                    return 'Nuova Prenotazione';
                }

                $date           = Carbon::parse($availability->dinnerDate->dinner_date)->isoFormat('dddd D MMMM YYYY');
                $hostName       = $availability->user->name;
                $availableSpots = $availability->available_spots;

                return "Prenota da {$hostName} - {$date} (Posti disponibili: {$availableSpots})";
            })
            ->modalSubmitActionLabel('Conferma Prenotazione')
            ->modalCancelActionLabel('Annulla')
            ->modalWidth('lg')
            ->schema([
                TextInput::make('guests_count')
                    ->label('Numero di ospiti')
                    ->helperText('Quante persone mangeranno?')
                    ->integer()
                    ->prefixIcon(Heroicon::OutlinedUsers)
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->rules([
                        function () {
                            return new ValidateBookingCapacity($this->bookingAvailabilityId);
                        },
                    ]),

                TagsInput::make('bringing_items')
                    ->label('Cosa porti?')
                    ->helperText('Vino, dolce, antipasto, ecc.')
                    ->placeholder('Aggiungi elemento')
                    ->separator(',')
                    ->nullable(),

                Textarea::make('notes')
                    ->label('Note e allergie')
                    ->helperText('Intolleranze alimentari, preferenze, note per l\'host')
                    ->rows(3)
                    ->maxLength(500)
                    ->nullable(),
            ])
            ->action(function (array $data) {
                try {
                    $availability = DinnerAvailability::findOrFail($this->bookingAvailabilityId);
                    $user         = Auth::user();

                    // Verifica autorizzazione tramite policy
                    // La policy già controlla:
                    // - Prenotazioni duplicate
                    // - Prenotazioni multiple nello stesso giorno
                    // - Prenotazioni cancellate esistenti
                    $policy = app(DinnerBookingPolicy::class);
                    if ( ! $policy->book($user, $availability)) {
                        Notification::make()
                            ->title('Non autorizzato')
                            ->body('Non puoi prenotare questa disponibilità. Verifica di non avere già una prenotazione per questo giorno.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Crea la prenotazione
                    DinnerBooking::create([
                        'host_availability_id' => $this->bookingAvailabilityId,
                        'guest_user_id'        => $user->id,
                        'guests_count'         => $data['guests_count'] ?? 0,
                        'bringing_items'       => $data['bringing_items'],
                        'notes'                => $data['notes'] ?? null,
                        'status'               => DinnerBookingStatus::PENDING,
                    ]);

                    // Observer gestirà automaticamente il cambio di stato dell'host e del guest

                    Notification::make()
                        ->title('Prenotazione confermata!')
                        ->body("Hai prenotato con successo per {$availability->user->name}")
                        ->success()
                        ->send();

                    // Ricarica i dati del calendario
                    $this->loadCalendarData();

                    // Reset delle proprietà
                    $this->bookingAvailabilityId = null;
                } catch (Exception $e) {
                    Notification::make()
                        ->title('Errore')
                        ->body('Si è verificato un errore durante la prenotazione: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
