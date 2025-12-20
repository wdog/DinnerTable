<?php

namespace Database\Seeders;

use App\Enums\DinnerAvailabilityStatus;
use App\Models\DinnerAvailability;
use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
                        'dinner_date' => $date->toDateString(),
                    ],
                    [
                        'is_closed' => false,
                        'notes' => null,
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
                    // Status random: 80% AVAILABLE, 15% MAYBE, 5% UNAVAILABLE
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 80) {
                        $status = DinnerAvailabilityStatus::AVAILABLE;
                    } elseif ($statusRand <= 95) {
                        $status = DinnerAvailabilityStatus::MAYBE;
                    } else {
                        $status = DinnerAvailabilityStatus::UNAVAILABLE;
                    }

                    // can_host: 30% true se status è AVAILABLE, altrimenti false
                    $canHost = false;
                    if ($status === DinnerAvailabilityStatus::AVAILABLE && rand(1, 100) <= 30) {
                        $canHost = true;
                    }

                    DinnerAvailability::create([
                        'dinner_date_id' => $dinnerDate->id,
                        'user_id' => $member->id,
                        'status' => $status,
                        'can_host' => $canHost,
                        'note' => $canHost ? 'Disponibile ad ospitare!' : null,
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
        $available = DinnerAvailability::where('status', DinnerAvailabilityStatus::AVAILABLE)->count();
        $maybe = DinnerAvailability::where('status', DinnerAvailabilityStatus::MAYBE)->count();
        $unavailable = DinnerAvailability::where('status', DinnerAvailabilityStatus::UNAVAILABLE)->count();
        $canHostCount = DinnerAvailability::where('can_host', true)->count();

        $this->command->info("  • Disponibili: {$available}");
        $this->command->info("  • Forse: {$maybe}");
        $this->command->info("  • Non disponibili: {$unavailable}");
        $this->command->info("  • Possono ospitare: {$canHostCount}");
    }
}
