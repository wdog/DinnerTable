<?php

namespace App\Filament\App\Pages;

use UnitEnum;
use Exception;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use App\Models\DinnerDate;
use Filament\Actions\Action;
use App\Models\DinnerBooking;
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
 * Pagina personalizzata con layout custom per mostrare il calendario
 * delle disponibilità dei membri del gruppo con navigazione tra i mesi.
 */
class GroupAvailabilities extends Page implements HasActions
{
    use InteractsWithActions;

    /**
     * Icona nella navigazione.
     */
    protected static string|BackedEnum|null $navigationIcon = 'tabler-calendar-user';

    /**
     * Label nella navigazione.
     */
    protected static ?string $navigationLabel = 'Calendario Disponibilità';

    /**
     * Vista blade della pagina.
     */
    protected string $view = 'filament.app.pages.group-availabilities';

    public ?string $bookingAvailabilityId = null;

    public array $bookingData = [];

    /**
     * Ottiene il titolo dinamico della pagina con il nome del gruppo.
     */
    public function getTitle(): string
    {
        $groupName = Auth::user()->dinnerGroup?->name;

        return $groupName
            ? "Disponibilità - {$groupName}"
            : 'Disponibilità del Gruppo';
    }

    /**
     * Ordine nella navigazione.
     */
    protected static ?int $navigationSort = 3;

    /**
     * Gruppo di navigazione.
     */
    protected static string|UnitEnum|null $navigationGroup = 'Gestione Cene';

    /**
     * Mese e anno selezionati (formato Y-m).
     */
    public ?string $selectedMonth = null;

    /**
     * Filtro per status disponibilità.
     */
    public ?string $filterStatus = null;

    /**
     * Filtro per "può ospitare".
     */
    public bool $filterCanHost = false;

    /**
     * Date del mese corrente con disponibilità.
     */
    public array $calendarData = [];

    /**
     * Inizializza il componente.
     */
    public function mount(): void
    {
        // Imposta il mese corrente come default
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Naviga al mese precedente.
     */
    public function previousMonth(): void
    {
        [$year, $month]      = explode('-', $this->selectedMonth);
        $date                = Carbon::create($year, $month, 1)->subMonth();
        $this->selectedMonth = $date->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Naviga al mese successivo.
     */
    public function nextMonth(): void
    {
        [$year, $month]      = explode('-', $this->selectedMonth);
        $date                = Carbon::create($year, $month, 1)->addMonth();
        $this->selectedMonth = $date->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Cambia il mese selezionato dal dropdown.
     */
    public function updatedSelectedMonth(): void
    {
        $this->loadCalendarData();
    }

    /**
     * Aggiorna il calendario quando cambia il filtro status.
     */
    public function updatedFilterStatus(): void
    {
        $this->loadCalendarData();
    }

    /**
     * Aggiorna il calendario quando cambia il filtro can_host.
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
     * @return array Opzioni del mese (chiave: Y-m, valore: label)
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
     * Carica i dati del calendario per il mese selezionato.
     *
     * Recupera tutte le date e disponibilità del gruppo per il mese corrente.
     * Costruisce una griglia di 7 colonne partendo da lunedì.
     */
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

                // ! Applica i filtri alle disponibilità
                $filteredAvailabilities = $availabilities->filter(function ($availability) {
                    // Filtro per status
                    if ($this->filterStatus && $availability->status->value !== $this->filterStatus) {
                        return false;
                    }

                    // TODO Filtro per can_host - in futuro devo vedere solo i prenotabili $can_host = true
                    if ($this->filterCanHost && ! $availability->can_host) {
                        return false;
                    }

                    return true;
                });

                $calendar[] = [
                    'empty'                => false,
                    'date'                 => $dinnerDate->dinner_date,
                    'day'                  => $day,
                    'day_name'             => Carbon::parse($dinnerDate->dinner_date)->isoFormat('ddd'),
                    'is_closed'            => $dinnerDate->is_closed,
                    'notes'                => $dinnerDate->notes,
                    'total_availabilities' => $filteredAvailabilities->count(),
                    'can_host_count'       => $filteredAvailabilities->where('can_host', true)->count(),
                    'availabilities'       => $filteredAvailabilities->map(function ($availability) {
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
                    'is_closed'            => false,
                    'notes'                => null,
                    'total_availabilities' => 0,
                    'available_count'      => 0,
                    'maybe_count'          => 0,
                    'unavailable_count'    => 0,
                    'can_host_count'       => 0,
                    'availabilities'       => [],
                ];
            }
        }

        $this->calendarData = $calendar;
    }

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
     * Verifica se l'utente corrente può prenotare una specifica disponibilità.
     */
    public function canBook(int $availabilityId): bool
    {
        $availability = DinnerAvailability::find($availabilityId);
        if ( ! $availability) {
            return false;
        }

        // Usa direttamente la policy
        $policy = app(DinnerBookingPolicy::class);

        return $policy->book(Auth::user(), $availability);
    }

    /**
     * Apre il modal di prenotazione per una specifica disponibilità.
     */
    public function openBookingModal(int $availabilityId): void
    {
        $this->bookingAvailabilityId = $availabilityId;
        $this->mountAction('createBooking');
    }

    /**
     * Action per creare una nuova prenotazione.
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
                    $policy = app(DinnerBookingPolicy::class);
                    if ( ! $policy->book($user, $availability)) {
                        Notification::make()
                            ->title('Non autorizzato')
                            ->body('Non puoi prenotare questa disponibilità.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // dd($data);
                    // Crea la prenotazione
                    DinnerBooking::create([
                        'host_availability_id' => $this->bookingAvailabilityId,
                        'guest_user_id'        => $user->id,
                        'guests_count'         => $data['guests_count'] ?? 0,
                        'bringing_items'       => $data['bringing_items'],
                        'notes'                => $data['notes'] ?? null,
                        'status'               => 'confirmed',
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
