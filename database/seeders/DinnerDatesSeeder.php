<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use App\Models\DinnerBooking;
use Illuminate\Database\Seeder;
use App\Enums\CancellationReason;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

/**
 * Seeder per creare disponibilità, eventi e prenotazioni su 3 mesi.
 *
 * Logica:
 * - Crea DinnerDate per ogni giorno di 3 mesi (precedente, corrente, successivo)
 * - 90% utenti diventano host, creano 3-5 eventi per mese
 * - TUTTI gli utenti creano 3 preferenze guest per mese
 * - Guest prenotano eventi host con partecipazione 70-100%
 * - Prenotazioni: 80% confirmed, 10% pending, 10% cancelled
 * - 10% eventi host nascono cancellati
 */
class DinnerDatesSeeder extends Seeder
{
    /**
     * ============================================
     * CONFIGURAZIONE CENTRALE DISPONIBILITÀ E PRENOTAZIONI
     * ============================================
     */

    // Periodo temporale
    private const MONTHS_TO_SEED = 3; // precedente, corrente, successivo

    // Distribuzione ruoli utenti (percentuale su 100)
    private const HOST_PERCENTAGE = 90;  // 90% degli utenti sono host

    // Eventi HOST per mese (3-5 garantiti)
    private const HOST_EVENTS_PER_MONTH_MIN = 3;

    private const HOST_EVENTS_PER_MONTH_MAX = 5;

    // Preferenze GUEST per mese (fisso 3 per TUTTI)
    private const GUEST_PREFERENCES_PER_MONTH = 3;

    // Capacità ospiti per evento host
    private const HOST_MAX_GUESTS_MIN = 4;

    private const HOST_MAX_GUESTS_MAX = 10;

    // Cancellazione eventi host (percentuale su 100)
    private const HOST_CANCELLATION_RATE = 10;  // 10% nascono cancellati

    // Partecipazione guest agli eventi (percentuale su 100)
    private const GUEST_PARTICIPATION_MIN = 70;  // min 70%

    private const GUEST_PARTICIPATION_MAX = 100; // max 100%

    // Ospiti per singola prenotazione
    private const GUESTS_PER_BOOKING_MIN = 1;

    private const GUESTS_PER_BOOKING_MAX = 2;

    // Distribuzione stati prenotazioni (percentuale su 100)
    // TUTTE nascono PENDING, poi vengono aggiornate
    private const BOOKING_CONFIRMED_PERCENTAGE = 80;

    private const BOOKING_PENDING_PERCENTAGE = 10;

    private const BOOKING_CANCELLED_PERCENTAGE = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🗓️  Inizio seeding disponibilità e prenotazioni (3 mesi)...');

        // STEP 1: Calcola i 3 mesi (precedente, corrente, successivo)
        $months = $this->calculateThreeMonths();

        $this->command->info("📆 Periodo: {$months['previous']['label']} → {$months['current']['label']} → {$months['next']['label']}");

        // STEP 2: Ottieni tutti i gruppi con membri
        $groups = DinnerGroup::with('members')->get();

        if ($groups->isEmpty()) {
            $this->command->warn('⚠️  Nessun gruppo trovato. Esegui prima DinnerGroupSeeder.');

            return;
        }

        // Statistiche globali
        $stats = [
            'total_dates'             => 0,
            'total_host_events'       => 0,
            'total_guest_preferences' => 0,
            'total_bookings'          => 0,
        ];

        // STEP 3: Per ogni gruppo
        foreach ($groups as $group) {
            $this->command->info("📅 Processando gruppo: {$group->name}");

            // STEP 3.1: Crea DinnerDate per tutti i giorni dei 3 mesi
            $dinnerDates = $this->createDinnerDatesForThreeMonths($group, $months);
            $stats['total_dates'] += $dinnerDates->count();

            // STEP 3.2: Assegna ruoli agli utenti (90% host, 10% solo guest)
            $roleAssignments = $this->assignUserRoles($group->members);

            $hostUsers = $roleAssignments['hosts'];
            $allUsers  = $roleAssignments['all'];

            $this->command->info("  👥 Utenti: {$allUsers->count()} totali, {$hostUsers->count()} host");

            // STEP 3.3: Crea eventi HOST per i 3 mesi
            $hostAvailabilities = $this->createHostEventsForThreeMonths(
                $group,
                $dinnerDates,
                $hostUsers,
                $months
            );
            $stats['total_host_events'] += $hostAvailabilities->count();

            $this->command->info("  🏠 Eventi HOST creati: {$hostAvailabilities->count()}");

            // STEP 3.4: Crea preferenze GUEST per TUTTI gli utenti (3 per mese)
            $guestPreferences = $this->createGuestPreferencesForThreeMonths(
                $group,
                $dinnerDates,
                $allUsers,
                $months
            );
            $stats['total_guest_preferences'] += $guestPreferences->count();

            $this->command->info("  👤 Preferenze GUEST create: {$guestPreferences->count()}");

            // STEP 3.5: Crea prenotazioni per eventi host disponibili
            $bookings = $this->createBookingsForHostEvents(
                $group,
                $hostAvailabilities,
                $allUsers
            );

            $this->command->info("  📝 Prenotazioni create (PENDING): {$bookings->count()}");

            // STEP 3.6: Aggiorna stati prenotazioni (PENDING → CONFIRMED/CANCELLED)
            $updatedBookings = $this->updateBookingStatuses($bookings);
            $stats['total_bookings'] += $updatedBookings->count();

            $confirmedCount = $updatedBookings->where('status', DinnerBookingStatus::CONFIRMED)->count();
            $this->command->info("  ✅ Prenotazioni confermate: {$confirmedCount}");
        }

        // STEP 4: Stampa statistiche dettagliate
        $this->printDetailedStatistics($stats, $groups, $months);
    }

    /**
     * Calcola i 3 mesi da seedare (precedente, corrente, successivo).
     *
     * @return array Array con 'previous', 'current', 'next' (Carbon instances)
     */
    protected function calculateThreeMonths(): array
    {
        $now = Carbon::now('Europe/Rome');

        return [
            'previous' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end'   => $now->copy()->subMonth()->endOfMonth(),
                'label' => $now->copy()->subMonth()->format('F Y'),
            ],
            'current' => [
                'start' => $now->copy()->startOfMonth(),
                'end'   => $now->copy()->endOfMonth(),
                'label' => $now->format('F Y'),
            ],
            'next' => [
                'start' => $now->copy()->addMonth()->startOfMonth(),
                'end'   => $now->copy()->addMonth()->endOfMonth(),
                'label' => $now->copy()->addMonth()->format('F Y'),
            ],
        ];
    }

    /**
     * Crea DinnerDate per ogni giorno dei 3 mesi.
     *
     * @param  array  $months  Array da calculateThreeMonths()
     * @return \Illuminate\Support\Collection Collection di DinnerDate creati
     */
    protected function createDinnerDatesForThreeMonths(DinnerGroup $group, array $months): \Illuminate\Support\Collection
    {
        $dinnerDates = collect();

        foreach ($months as $monthKey => $monthData) {
            $currentDate = $monthData['start']->copy();

            while ($currentDate->lte($monthData['end'])) {
                $dinnerDate = DinnerDate::firstOrCreate([
                    'dinner_group_id' => $group->id,
                    'dinner_date'     => $currentDate->toDateString(),
                ]);

                $dinnerDates->push($dinnerDate);
                $currentDate->addDay();
            }
        }

        return $dinnerDates;
    }

    /**
     * Assegna ruoli agli utenti del gruppo.
     *
     * 90% diventano host, 10% rimangono solo guest.
     * TUTTI possono creare preferenze guest.
     *
     * @param  \Illuminate\Support\Collection  $members  Membri del gruppo
     * @return array ['hosts' => Collection, 'all' => Collection]
     */
    protected function assignUserRoles(\Illuminate\Support\Collection $members): array
    {
        $shuffled  = $members->shuffle();
        $hostCount = (int) ceil($members->count() * (self::HOST_PERCENTAGE / 100));

        $hosts = $shuffled->take($hostCount);

        return [
            'hosts' => $hosts,
            'all'   => $members,
        ];
    }

    /**
     * Crea eventi HOST per i 3 mesi.
     *
     * Ogni host crea 3-5 eventi PER MESE (garantiti).
     * 90% nascono AVAILABLE_TO_HOST, 10% HOST_CANCELLED.
     *
     * @param  \Illuminate\Support\Collection  $dinnerDates  Tutte le date disponibili (3 mesi)
     * @param  \Illuminate\Support\Collection  $hostUsers  Utenti con ruolo host
     * @param  array  $months  Array da calculateThreeMonths()
     * @return \Illuminate\Support\Collection Collection di DinnerAvailability host create
     */
    protected function createHostEventsForThreeMonths(
        DinnerGroup $group,
        \Illuminate\Support\Collection $dinnerDates,
        \Illuminate\Support\Collection $hostUsers,
        array $months
    ): \Illuminate\Support\Collection {
        $hostAvailabilities = collect();

        foreach ($hostUsers as $hostUser) {
            // Per ogni mese, crea 3-5 eventi
            foreach ($months as $monthKey => $monthData) {
                $numEvents = rand(
                    self::HOST_EVENTS_PER_MONTH_MIN,
                    self::HOST_EVENTS_PER_MONTH_MAX
                );

                // Filtra date del mese corrente
                $monthDates = $dinnerDates->filter(function ($date) use ($monthData) {
                    $dinnerDate = Carbon::parse($date->dinner_date);

                    return $dinnerDate->between(
                        $monthData['start'],
                        $monthData['end']
                    );
                });

                // Seleziona N date random dal mese
                $selectedDates = $monthDates
                    ->shuffle()
                    ->take($numEvents)
                    ->values();

                // Crea disponibilità host per ogni data
                foreach ($selectedDates as $dinnerDate) {
                    // Verifica che non esista già
                    $existing = DinnerAvailability::where('dinner_date_id', $dinnerDate->id)
                        ->where('user_id', $hostUser->id)
                        ->exists();

                    if ($existing) {
                        continue;
                    }

                    // Determina se cancellato (10% probabilità)
                    $isCancelled = rand(1, 100) <= self::HOST_CANCELLATION_RATE;

                    if ($isCancelled) {
                        $status             = DinnerAvailabilityStatus::HOST_CANCELLED;
                        $cancellationReason = collect([
                            CancellationReason::PERSONAL_EMERGENCY,
                            CancellationReason::ILLNESS,
                            CancellationReason::WORK_COMMITMENT,
                            CancellationReason::FAMILY_REASON,
                            CancellationReason::HOUSE_ISSUE,
                            CancellationReason::OTHER,
                        ])->random();
                    } else {
                        $status             = DinnerAvailabilityStatus::AVAILABLE_TO_HOST;
                        $cancellationReason = null;
                    }

                    $maxGuests  = rand(self::HOST_MAX_GUESTS_MIN, self::HOST_MAX_GUESTS_MAX);
                    $dinnerName = $this->generateRandomDinnerName();

                    $availability = DinnerAvailability::create([
                        'dinner_date_id'      => $dinnerDate->id,
                        'user_id'             => $hostUser->id,
                        'status'              => $status,
                        'can_host'            => true,
                        'dinner_name'         => $dinnerName,
                        'max_guests'          => $maxGuests,
                        'note'                => 'Disponibile ad ospitare!',
                        'cancellation_reason' => $cancellationReason,
                    ]);

                    $hostAvailabilities->push($availability);
                }
            }
        }

        return $hostAvailabilities;
    }

    /**
     * Crea preferenze GUEST per TUTTI gli utenti (anche host).
     *
     * Ogni utente crea esattamente 3 preferenze PER MESE.
     * Totale: 9 preferenze per utente (3 mesi × 3).
     *
     * @param  \Illuminate\Support\Collection  $dinnerDates  Tutte le date disponibili
     * @param  \Illuminate\Support\Collection  $allUsers  TUTTI i membri (host + only-guest)
     * @param  array  $months  Array da calculateThreeMonths()
     * @return \Illuminate\Support\Collection Collection di DinnerAvailability guest create
     */
    protected function createGuestPreferencesForThreeMonths(
        DinnerGroup $group,
        \Illuminate\Support\Collection $dinnerDates,
        \Illuminate\Support\Collection $allUsers,
        array $months
    ): \Illuminate\Support\Collection {
        $guestPreferences = collect();

        foreach ($allUsers as $user) {
            // Per ogni mese, crea esattamente 3 preferenze
            foreach ($months as $monthKey => $monthData) {
                // Filtra date del mese
                $monthDates = $dinnerDates->filter(function ($date) use ($monthData) {
                    $dinnerDate = Carbon::parse($date->dinner_date);

                    return $dinnerDate->between(
                        $monthData['start'],
                        $monthData['end']
                    );
                });

                // Seleziona 3 date random dal mese
                $selectedDates = $monthDates
                    ->shuffle()
                    ->take(self::GUEST_PREFERENCES_PER_MONTH)
                    ->values();

                // Crea preferenza guest per ogni data
                foreach ($selectedDates as $dinnerDate) {
                    // Verifica che l'utente non abbia già una disponibilità per questa data
                    $existing = DinnerAvailability::where('dinner_date_id', $dinnerDate->id)
                        ->where('user_id', $user->id)
                        ->exists();

                    if ($existing) {
                        // Utente ha già evento host per questa data, skip
                        continue;
                    }

                    $preference = DinnerAvailability::create([
                        'dinner_date_id'      => $dinnerDate->id,
                        'user_id'             => $user->id,
                        'status'              => DinnerAvailabilityStatus::AVAILABLE,
                        'can_host'            => false,
                        'dinner_name'         => null,
                        'max_guests'          => null,
                        'note'                => null,
                        'cancellation_reason' => null,
                    ]);

                    $guestPreferences->push($preference);
                }
            }
        }

        return $guestPreferences;
    }

    /**
     * Crea prenotazioni per eventi host disponibili.
     *
     * Solo eventi con status AVAILABLE_TO_HOST possono ricevere prenotazioni.
     * Partecipazione: 70-100% dei guest disponibili prenota.
     * TUTTE le prenotazioni nascono con status PENDING.
     *
     * VALIDAZIONI:
     * - Non prenotare da se stessi
     * - Max 1 prenotazione per guest per data
     * - Rispettare max_guests dell'host
     * - Numero ospiti: 1-2 per prenotazione
     *
     * @param  \Illuminate\Support\Collection  $hostAvailabilities  Eventi host creati
     * @param  \Illuminate\Support\Collection  $allUsers  TUTTI gli utenti del gruppo
     * @return \Illuminate\Support\Collection Collection di DinnerBooking create (status PENDING)
     */
    protected function createBookingsForHostEvents(
        DinnerGroup $group,
        \Illuminate\Support\Collection $hostAvailabilities,
        \Illuminate\Support\Collection $allUsers
    ): \Illuminate\Support\Collection {
        $bookings = collect();

        // Filtra solo host disponibili (status = AVAILABLE_TO_HOST)
        $availableHosts = $hostAvailabilities->filter(function ($availability) {
            return $availability->status === DinnerAvailabilityStatus::AVAILABLE_TO_HOST;
        });

        foreach ($availableHosts as $hostAvailability) {
            // Determina partecipazione random (70-100%)
            $participationRate = rand(
                self::GUEST_PARTICIPATION_MIN,
                self::GUEST_PARTICIPATION_MAX
            );

            // Filtra utenti eleggibili (escludi host stesso)
            $eligibleGuests = $allUsers->filter(function ($user) use ($hostAvailability) {
                return $user->id !== $hostAvailability->user_id;
            });

            // Applica tasso partecipazione
            $participatingGuests = $eligibleGuests->filter(function ($guest) use ($participationRate) {
                return rand(1, 100) <= $participationRate;
            });

            $remainingSpots = $hostAvailability->max_guests;
            $bookedDates    = []; // Track guest-date combinations

            // Shuffle per randomizzare ordine
            foreach ($participatingGuests->shuffle() as $guest) {
                if ($remainingSpots <= 0) {
                    break; // Host pieno
                }

                // Verifica che guest non abbia già prenotato per questa data
                $dateKey = $hostAvailability->dinner_date_id . '-' . $guest->id;
                if (in_array($dateKey, $bookedDates)) {
                    continue;
                }

                // Numero ospiti per questa prenotazione (1-2)
                $guestsCount = rand(
                    self::GUESTS_PER_BOOKING_MIN,
                    min(self::GUESTS_PER_BOOKING_MAX, $remainingSpots)
                );

                // Items random
                $bringingItems = $this->generateRandomItems();

                // Note random (50% probabilità)
                $notes = rand(1, 100) <= 50 ? $this->generateRandomNote() : null;

                // Crea booking con status PENDING
                $booking = DinnerBooking::create([
                    'host_availability_id' => $hostAvailability->id,
                    'guest_user_id'        => $guest->id,
                    'guests_count'         => $guestsCount,
                    'bringing_items'       => $bringingItems,
                    'notes'                => $notes,
                    'status'               => DinnerBookingStatus::PENDING, // TUTTE nascono PENDING
                ]);

                $bookings->push($booking);
                $bookedDates[] = $dateKey;
                $remainingSpots -= $guestsCount;
            }
        }

        return $bookings;
    }

    /**
     * Aggiorna stati prenotazioni da PENDING a CONFIRMED/CANCELLED.
     *
     * Distribuzione finale:
     * - 80% → CONFIRMED (Observer aggiornerà status host)
     * - 10% → PENDING (rimangono)
     * - 10% → CANCELLED
     *
     * IMPORTANTE: Quando status diventa CONFIRMED, DinnerBookingObserver
     * aggiornerà automaticamente lo status host (AVAILABLE_TO_HOST → ALMOST_FULL → FULL).
     *
     * @param  \Illuminate\Support\Collection  $bookings  Prenotazioni con status PENDING
     * @return \Illuminate\Support\Collection Prenotazioni aggiornate
     */
    protected function updateBookingStatuses(\Illuminate\Support\Collection $bookings): \Illuminate\Support\Collection
    {
        $updatedBookings = collect();

        foreach ($bookings as $booking) {
            $rand = rand(1, 100);

            if ($rand <= self::BOOKING_CONFIRMED_PERCENTAGE) {
                // 80% → CONFIRMED
                $booking->update(['status' => DinnerBookingStatus::CONFIRMED]);
                // Observer aggiornerà host status automaticamente
            } elseif ($rand <= self::BOOKING_CONFIRMED_PERCENTAGE + self::BOOKING_PENDING_PERCENTAGE) {
                // 10% → PENDING (già PENDING, nessuna modifica)
                // Non fare nulla
            } else {
                // 10% → CANCELLED
                $booking->update(['status' => DinnerBookingStatus::CANCELLED]);
            }

            // Ricarica il model per avere lo stato aggiornato
            $updatedBookings->push($booking->fresh());
        }

        return $updatedBookings;
    }

    /**
     * Genera nome cena random (70% con nome, 30% null).
     */
    protected function generateRandomDinnerName(): ?string
    {
        if (rand(1, 100) <= 30) {
            return null;
        }

        $names = [
            'Pizza Napoletana',
            'Pasta al Forno',
            'Sushi Night',
            'BBQ Serata',
            'Carbonara Perfetta',
            'Risotto ai Funghi',
            'Lasagne della Nonna',
            'Grigliata Mista',
            'Pesce Fresco',
            'Serata Vegan',
            'Aperitivo Italiano',
            'Cena Messicana',
            'Burger Gourmet',
            'Pasta Fresca',
            'Cucina Giapponese',
        ];

        return collect($names)->random();
    }

    /**
     * Genera items random che il guest porta (0-2 items).
     */
    protected function generateRandomItems(): array
    {
        $possibleItems = ['Vino', 'Dolce', 'Antipasto', 'Frutta', 'Pane', 'Acqua'];
        $numItems      = rand(0, 2);

        return $numItems > 0
            ? collect($possibleItems)->random($numItems)->values()->toArray()
            : [];
    }

    /**
     * Genera nota random per booking.
     */
    protected function generateRandomNote(): ?string
    {
        $notes = [
            'Non vedo l\'ora!',
            'Grazie per l\'ospitalità',
            'Arriverò verso le 20:00',
            'Ho alcune intolleranze alimentari',
            null,
        ];

        return collect($notes)->random();
    }

    /**
     * Stampa statistiche dettagliate post-seeding.
     *
     * Include:
     * - Totali globali
     * - Statistiche per mese
     * - Statistiche per gruppo
     * - Distribuzione stati
     */
    protected function printDetailedStatistics(array $stats, \Illuminate\Support\Collection $groups, array $months): void
    {
        $this->command->newLine();
        $this->command->info('🎉 Seeding completato!');
        $this->command->newLine();

        // Totali globali
        $this->command->info('📊 TOTALI GLOBALI:');
        $this->command->info("   📅 Date create: {$stats['total_dates']}");
        $this->command->info("   🏠 Eventi HOST: {$stats['total_host_events']}");
        $this->command->info("   👤 Preferenze GUEST: {$stats['total_guest_preferences']}");
        $this->command->info("   📝 Prenotazioni: {$stats['total_bookings']}");

        // Statistiche per mese
        $this->command->newLine();
        $this->command->info('📆 STATISTICHE PER MESE:');

        foreach ($months as $key => $monthData) {
            $label = $monthData['label'];

            $hostEvents = DinnerAvailability::where('can_host', true)
                ->whereHas('dinnerDate', function ($q) use ($monthData) {
                    $q->whereBetween('dinner_date', [
                        $monthData['start']->toDateString(),
                        $monthData['end']->toDateString(),
                    ]);
                })
                ->count();

            $guestPrefs = DinnerAvailability::where('can_host', false)
                ->whereHas('dinnerDate', function ($q) use ($monthData) {
                    $q->whereBetween('dinner_date', [
                        $monthData['start']->toDateString(),
                        $monthData['end']->toDateString(),
                    ]);
                })
                ->count();

            $this->command->info("  • {$label}:");
            $this->command->info("    - Eventi HOST: {$hostEvents}");
            $this->command->info("    - Preferenze GUEST: {$guestPrefs}");
        }

        // Statistiche per gruppo
        $this->command->newLine();
        $this->command->info('👥 STATISTICHE PER GRUPPO:');

        foreach ($groups as $group) {
            $datesCount  = DinnerDate::where('dinner_group_id', $group->id)->count();
            $availsCount = DinnerAvailability::whereHas('dinnerDate', fn ($q) => $q->where('dinner_group_id', $group->id)
            )->count();

            $this->command->info("  • {$group->name}:");
            $this->command->info("    - Date: {$datesCount}");
            $this->command->info("    - Disponibilità totali: {$availsCount}");
        }

        // Statistiche stati
        $this->command->newLine();
        $this->command->info('📈 DISTRIBUZIONE STATI:');

        // Stati host
        $availableToHost = DinnerAvailability::where('status', DinnerAvailabilityStatus::AVAILABLE_TO_HOST)->count();
        $almostFull      = DinnerAvailability::where('status', DinnerAvailabilityStatus::ALMOST_FULL)->count();
        $full            = DinnerAvailability::where('status', DinnerAvailabilityStatus::FULL)->count();
        $hostCancelled   = DinnerAvailability::where('status', DinnerAvailabilityStatus::HOST_CANCELLED)->count();

        $this->command->info('  Host stati:');
        $this->command->info("    • AVAILABLE_TO_HOST: {$availableToHost}");
        $this->command->info("    • ALMOST_FULL: {$almostFull}");
        $this->command->info("    • FULL: {$full}");
        $this->command->info("    • HOST_CANCELLED: {$hostCancelled}");

        // Stati guest
        $available = DinnerAvailability::where('status', DinnerAvailabilityStatus::AVAILABLE)->count();

        $this->command->info('  Guest stati:');
        $this->command->info("    • AVAILABLE: {$available}");

        // Stati bookings
        $pending   = DinnerBooking::where('status', DinnerBookingStatus::PENDING)->count();
        $confirmed = DinnerBooking::where('status', DinnerBookingStatus::CONFIRMED)->count();
        $cancelled = DinnerBooking::where('status', DinnerBookingStatus::CANCELLED)->count();

        $this->command->newLine();
        $this->command->info('  Booking stati:');
        $this->command->info("    • PENDING: {$pending}");
        $this->command->info("    • CONFIRMED: {$confirmed}");
        $this->command->info("    • CANCELLED: {$cancelled}");
    }
}
