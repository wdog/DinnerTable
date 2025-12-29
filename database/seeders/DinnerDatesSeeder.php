<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use App\Models\DinnerBooking;
use Illuminate\Database\Seeder;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

class DinnerDatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🗓️  Inizio seeding date e disponibilità dicembre...');

        // Ottieni tutti i gruppi
        $groups = DinnerGroup::with('members')->get();

        if ($groups->isEmpty()) {
            $this->command->warn('⚠️  Nessun gruppo trovato. Esegui prima DinnerGroupSeeder.');

            return;
        }

        $totalDates          = 0;
        $totalAvailabilities = 0;

        // Per ogni gruppo, crea le date di dicembre
        foreach ($groups as $group) {
            $this->command->info("📅 Creazione date per gruppo: {$group->name}");

            $startDate = Carbon::create(2025, 12, 1);
            $endDate   = Carbon::create(2026, 3, 31);

            $dates = [];

            // Crea una data per ogni giorno di dicembre
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dinnerDate = DinnerDate::firstOrCreate(
                    [
                        'dinner_group_id' => $group->id,
                        'dinner_date'     => $date->toDateString(),
                    ],
                    [
                    ]
                );

                $dates[] = $dinnerDate;
                if ($dinnerDate->wasRecentlyCreated) {
                    $totalDates++;
                }
            }

            $this->command->info('  ✓ Create ' . count($dates) . ' date per il gruppo');

            // Per ogni membro del gruppo, crea molte più disponibilità
            foreach ($group->members as $member) {
                // Numero più alto di disponibilità (60-90 date per avere più eventi)
                $numAvailabilities = rand(60, 90);

                // Seleziona date random dal periodo
                $selectedDates = collect($dates)
                    ->random(min($numAvailabilities, count($dates)))
                    ->values();

                foreach ($selectedDates as $dinnerDate) {
                    // Aumenta probabilità di poter ospitare (50% invece di 30%)
                    $canHost = rand(1, 100) <= 50;

                    if ($canHost) {
                        // HOST: usa solo stati iniziali validi
                        // AVAILABLE_TO_HOST è l'unico stato iniziale per un host
                        // ALMOST_FULL, FULL vengono impostati automaticamente dall'Observer quando ci sono prenotazioni
                        // HOST_CANCELLED può essere impostato solo manualmente dall'utente
                        $status = DinnerAvailabilityStatus::AVAILABLE_TO_HOST;

                        // Max guests random tra 4 e 10
                        $maxGuests = rand(4, 10);

                        // Titoli cena vari e invitanti
                        $dinnerTitles = [
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
                            null, // 30% senza titolo
                            null,
                            null,
                        ];

                        $dinnerName = collect($dinnerTitles)->random();
                    } else {
                        // GUEST: usa sempre AVAILABLE (unico stato per guest)
                        $status     = DinnerAvailabilityStatus::AVAILABLE;
                        $maxGuests  = null;
                        $dinnerName = null;
                    }

                    $availability = DinnerAvailability::firstOrCreate(
                        [
                            'dinner_date_id' => $dinnerDate->id,
                            'user_id'        => $member->id,
                        ],
                        [
                            'status'      => $status,
                            'can_host'    => $canHost,
                            'dinner_name' => $dinnerName,
                            'max_guests'  => $maxGuests,
                            'note'        => $canHost ? 'Disponibile ad ospitare!' : null,
                        ]
                    );

                    if ($availability->wasRecentlyCreated) {
                        $totalAvailabilities++;
                    }
                }
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Seeding date e disponibilità completato!');
        $this->command->info("   📅 Date create: {$totalDates}");
        $this->command->info("   ✅ Disponibilità create: {$totalAvailabilities}");

        // Crea prenotazioni
        $this->command->newLine();
        $this->command->info('🍽️  Inizio creazione prenotazioni...');
        $totalBookings = $this->createBookings($groups);
        $this->command->info("   ✅ Prenotazioni create: {$totalBookings}");

        // Statistiche per gruppo
        $this->command->newLine();
        $this->command->info('📊 Statistiche per gruppo:');

        foreach ($groups as $group) {
            $datesCount  = DinnerDate::where('dinner_group_id', $group->id)->count();
            $availsCount = DinnerAvailability::whereHas('dinnerDate', function ($q) use ($group) {
                $q->where('dinner_group_id', $group->id);
            })->count();

            $avgPerMember = $group->members->count() > 0
                ? round($availsCount / $group->members->count(), 1)
                : 0;

            $this->command->info("  • {$group->name}:");
            $this->command->info("    - Date: {$datesCount}");
            $this->command->info("    - Disponibilità: {$availsCount}");
            $this->command->info("    - Media per membro: {$avgPerMember}");
        }

        // Statistiche per status
        $this->command->newLine();
        $this->command->info('📈 Statistiche per status:');

        // Stati HOST
        $availableToHost = DinnerAvailability::where('status', DinnerAvailabilityStatus::AVAILABLE_TO_HOST)->count();
        $almostFull      = DinnerAvailability::where('status', DinnerAvailabilityStatus::ALMOST_FULL)->count();
        $full            = DinnerAvailability::where('status', DinnerAvailabilityStatus::FULL)->count();
        $hostCancelled   = DinnerAvailability::where('status', DinnerAvailabilityStatus::HOST_CANCELLED)->count();

        // Stati GUEST
        $available = DinnerAvailability::where('status', DinnerAvailabilityStatus::AVAILABLE)->count();

        $canHostCount = DinnerAvailability::where('can_host', true)->count();

        $this->command->info('  Host stati:');
        $this->command->info("    • Disponibili ad ospitare: {$availableToHost}");
        $this->command->info("    • Quasi pieni: {$almostFull}");
        $this->command->info("    • Pieni: {$full}");
        $this->command->info("    • Annullati: {$hostCancelled}");
        $this->command->newLine();
        $this->command->info('  Guest stati:');
        $this->command->info("    • Disponibili: {$available}");
        $this->command->newLine();
        $this->command->info("  • Totale che possono ospitare: {$canHostCount}");

        // Statistiche prenotazioni
        $this->command->newLine();
        $this->command->info('📋 Statistiche prenotazioni:');

        $pending   = DinnerBooking::where('status', DinnerBookingStatus::PENDING)->count();
        $confirmed = DinnerBooking::where('status', DinnerBookingStatus::CONFIRMED)->count();
        $cancelled = DinnerBooking::where('status', DinnerBookingStatus::CANCELLED)->count();

        $this->command->info("  • In attesa: {$pending}");
        $this->command->info("  • Confermate: {$confirmed}");
        $this->command->info("  • Cancellate: {$cancelled}");
    }

    /**
     * Crea prenotazioni realistiche rispettando le regole del sistema.
     *
     * Regole rispettate:
     * - Solo guest possono prenotare (can_host = false)
     * - Solo presso host disponibili (can_host = true, status = AVAILABLE_TO_HOST)
     * - Rispetta capacità massima (max_guests)
     * - Stesso gruppo
     * - Non puoi prenotare da te stesso
     * - Stati: 60% confirmed, 30% pending, 10% cancelled
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $groups
     * @return int Numero di prenotazioni create
     */
    protected function createBookings($groups): int
    {
        $totalBookings = 0;

        foreach ($groups as $group) {
            // Ottieni tutti gli host disponibili del gruppo
            $availableHosts = DinnerAvailability::where('can_host', true)
                ->where('status', DinnerAvailabilityStatus::AVAILABLE_TO_HOST)
                ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_group_id', $group->id))
                ->with(['user', 'dinnerDate'])
                ->get();

            if ($availableHosts->isEmpty()) {
                continue;
            }

            // Ottieni tutti i guest del gruppo (utenti con almeno una disponibilità da guest)
            $potentialGuests = DinnerAvailability::where('can_host', false)
                ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_group_id', $group->id))
                ->with('user')
                ->get()
                ->pluck('user')
                ->unique('id');

            if ($potentialGuests->isEmpty()) {
                continue;
            }

            // Per ogni host, crea più prenotazioni (2-5 invece di 1-3)
            foreach ($availableHosts as $hostAvailability) {
                $numBookings = rand(2, 5);

                // Calcola posti disponibili
                $availableSpots = $hostAvailability->max_guests;

                // Seleziona guest random (diversi dall'host)
                $selectedGuests = $potentialGuests
                    ->where('id', '!=', $hostAvailability->user_id)
                    ->shuffle()
                    ->take(min($numBookings, $availableSpots));

                foreach ($selectedGuests as $guest) {
                    // Verifica che il guest non abbia già prenotato per questa data
                    $alreadyBooked = DinnerBooking::where('guest_user_id', $guest->id)
                        ->whereHas('hostAvailability.dinnerDate', function ($q) use ($hostAvailability) {
                            $q->where('id', $hostAvailability->dinner_date_id);
                        })
                        ->exists();

                    if ($alreadyBooked) {
                        continue;
                    }

                    // Calcola posti ancora disponibili
                    $currentBookings = $hostAvailability->total_booked_guests;
                    if ($currentBookings >= $hostAvailability->max_guests) {
                        break; // Host pieno
                    }

                    // Numero di ospiti random (1-2 per prenotazione)
                    $guestsCount = rand(1, min(2, $hostAvailability->max_guests - $currentBookings));

                    // Determina lo stato (50% confirmed, 40% pending, 10% cancelled)
                    // Più pending per avere più prenotazioni da confermare
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 50) {
                        $status = DinnerBookingStatus::CONFIRMED;
                    } elseif ($statusRand <= 90) {
                        $status = DinnerBookingStatus::PENDING;
                    } else {
                        $status = DinnerBookingStatus::CANCELLED;
                    }

                    // Items random che il guest porta
                    $possibleItems = ['Vino', 'Dolce', 'Antipasto', 'Frutta', 'Pane', 'Acqua'];
                    $numItems      = rand(0, 2);
                    $bringingItems = $numItems > 0
                        ? collect($possibleItems)->random($numItems)->values()->toArray()
                        : [];

                    // Note random (50% probabilità)
                    $notes = rand(1, 100) <= 50
                        ? collect([
                            'Non vedo l\'ora!',
                            'Grazie per l\'ospitalità',
                            'Arriverò verso le 20:00',
                            'Ho alcune intolleranze alimentari',
                            null,
                        ])->random()
                        : null;

                    DinnerBooking::create([
                        'host_availability_id' => $hostAvailability->id,
                        'guest_user_id'        => $guest->id,
                        'guests_count'         => $guestsCount,
                        'bringing_items'       => $bringingItems,
                        'notes'                => $notes,
                        'status'               => $status,
                    ]);

                    $totalBookings++;

                    // Ricarica per aggiornare il conteggio
                    $hostAvailability->refresh();
                }
            }
        }

        return $totalBookings;
    }
}
