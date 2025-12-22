<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use Illuminate\Database\Seeder;
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

        $totalDates = 0;
        $totalAvailabilities = 0;

        // Per ogni gruppo, crea le date di dicembre
        foreach ($groups as $group) {
            $this->command->info("📅 Creazione date per gruppo: {$group->name}");

            // Crea date per tutto dicembre 2025
            $startDate = Carbon::create(2025, 12, 1);
            $endDate = Carbon::create(2025, 12, 31);

            $dates = [];

            // Crea una data per ogni giorno di dicembre
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dinnerDate = DinnerDate::firstOrCreate(
                    [
                        'dinner_group_id' => $group->id,
                        'dinner_date'     => $date->toDateString(),
                    ],
                    [
                        'is_closed' => false,
                        'notes'     => null,
                    ]
                );

                $dates[] = $dinnerDate;
                if ($dinnerDate->wasRecentlyCreated) {
                    $totalDates++;
                }
            }

            $this->command->info('  ✓ Create '.count($dates).' date per il gruppo');

            // Per ogni membro del gruppo, crea disponibilità random (0-4 date)
            foreach ($group->members as $member) {
                // Numero random di disponibilità (0-4)
                $numAvailabilities = rand(0, 4);

                if ($numAvailabilities === 0) {
                    continue;
                }

                // Seleziona date random dal mese
                $selectedDates = collect($dates)
                    ->random(min($numAvailabilities, count($dates)))
                    ->values();

                foreach ($selectedDates as $dinnerDate) {
                    // Decidi prima se può ospitare (30% probabilità)
                    $canHost = rand(1, 100) <= 30;

                    if ($canHost) {
                        // HOST: usa solo stati iniziali validi
                        // AVAILABLE_TO_HOST è l'unico stato iniziale per un host
                        // ALMOST_FULL, FULL vengono impostati automaticamente dall'Observer quando ci sono prenotazioni
                        // HOST_CANCELLED può essere impostato solo manualmente dall'utente
                        $status = DinnerAvailabilityStatus::AVAILABLE_TO_HOST;

                        // Max guests random tra 4 e 10
                        $maxGuests = rand(4, 10);
                    } else {
                        // GUEST: usa sempre AVAILABLE (unico stato per guest)
                        $status = DinnerAvailabilityStatus::AVAILABLE;
                        $maxGuests = null;
                    }

                    DinnerAvailability::create([
                        'dinner_date_id' => $dinnerDate->id,
                        'user_id'        => $member->id,
                        'status'         => $status,
                        'can_host'       => $canHost,
                        'max_guests'     => $maxGuests,
                        'note'           => $canHost ? 'Disponibile ad ospitare!' : null,
                    ]);

                    $totalAvailabilities++;
                }
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Seeding date e disponibilità completato!');
        $this->command->info("   📅 Date create: {$totalDates}");
        $this->command->info("   ✅ Disponibilità create: {$totalAvailabilities}");

        // Statistiche per gruppo
        $this->command->newLine();
        $this->command->info('📊 Statistiche per gruppo:');

        foreach ($groups as $group) {
            $datesCount = DinnerDate::where('dinner_group_id', $group->id)->count();
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
        $almostFull = DinnerAvailability::where('status', DinnerAvailabilityStatus::ALMOST_FULL)->count();
        $full = DinnerAvailability::where('status', DinnerAvailabilityStatus::FULL)->count();
        $hostCancelled = DinnerAvailability::where('status', DinnerAvailabilityStatus::HOST_CANCELLED)->count();

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
    }
}
