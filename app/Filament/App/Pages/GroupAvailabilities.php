<?php

namespace App\Filament\App\Pages;

use UnitEnum;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use App\Models\DinnerDate;
use Filament\Actions\Action;
use App\Models\DinnerBooking;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Auth;
use App\Enums\DinnerAvailabilityStatus;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\TagsInput;

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
        [$year, $month] = explode('-', $this->selectedMonth);
        $date = Carbon::create($year, $month, 1)->subMonth();
        $this->selectedMonth = $date->format('Y-m');
        $this->loadCalendarData();
    }

    /**
     * Naviga al mese successivo.
     */
    public function nextMonth(): void
    {
        [$year, $month] = explode('-', $this->selectedMonth);
        $date = Carbon::create($year, $month, 1)->addMonth();
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
        if (! $this->selectedMonth) {
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
        $start = Carbon::now()->subMonths(2);
        $end = Carbon::now()->addMonths(12);

        for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
            $key = $date->format('Y-m');
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
        if (! $this->selectedMonth) {
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
        $daysInMonth = $firstDayOfMonth->daysInMonth;

        // Calcola l'offset per iniziare da lunedì (1 = lunedì, 7 = domenica)
        $dayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1 (lunedì) a 7 (domenica)
        $offset = $dayOfWeek - 1; // 0 per lunedì, 6 per domenica

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

                    if ($availability->status === DinnerAvailabilityStatus::UNAVAILABLE) {
                        return false;
                    }

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

                $calendar[] = [
                    'empty' => false,
                    'date' => $dinnerDate->dinner_date,
                    'day' => $day,
                    'day_name' => Carbon::parse($dinnerDate->dinner_date)->isoFormat('ddd'),
                    'is_closed' => $dinnerDate->is_closed,
                    'notes' => $dinnerDate->notes,
                    'total_availabilities' => $filteredAvailabilities->count(),
                    'can_host_count' => $filteredAvailabilities->where('can_host', true)->count(),
                    'availabilities' => $filteredAvailabilities->map(function ($availability) {
                        return [
                            'id' => $availability->id,
                            'user_name' => $availability->user->name,
                            'status' => $availability->status,
                            'can_host' => $availability->can_host,
                            'note' => $availability->note,
                            'can_book' => (int) $this->canBook($availability->id),
                        ];
                    })->toArray(),
                ];
            } else {
                // Giorno senza dati nel database
                $currentDate = Carbon::create($year, $month, $day);
                $calendar[] = [
                    'empty' => false,
                    'date' => $currentDate,
                    'day' => $day,
                    'day_name' => $currentDate->isoFormat('ddd'),
                    'is_closed' => false,
                    'notes' => null,
                    'total_availabilities' => 0,
                    'available_count' => 0,
                    'maybe_count' => 0,
                    'unavailable_count' => 0,
                    'can_host_count' => 0,
                    'availabilities' => [],
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

    public function canBook(int $availabilityId): bool
    {
        $availability = DinnerAvailability::find($availabilityId);
        // TODO manca il controllo sullo stato e sul numero di ospiti
        if ($availability->can_host) {
            return true;
        }

        return false;
    }

    public function openEditModal($bookingAvailabilityId): void
    {
        $this->bookingAvailabilityId = $bookingAvailabilityId;
        $this->mountAction('createBooking');
    }

    public function createBooking(): Action
    {
        return  Action::make('createBooking')
            // ->modalHeading('Nuova prenotazione')
            ->modalHeading(fn() => "Prenotazione per #{$this->bookingAvailabilityId}")
            ->modalSubmitActionLabel('Salva')
            ->modalWidth('md')
            ->schema([
                TextInput::make('guests_count')
                    ->label('Numero ospiti')
                    ->integer()
                    ->minValue(1)
                    ->required(),

                TextInput::make('bringing_items')
                    ->label('Cosa porto?'),

                TextInput::make('notes')
                    ->label('Note organizzatore'),
            ])
            ->mountUsing(function (Action $action) {
                $action->data([
                    'bookingAvailabilityId' => $this->bookingAvailabilityId,
                ]);
            })
            ->action(fn(array $data) => dd($data));
    }
    /** --- */
}
