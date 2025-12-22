<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;

/**
 * Comando per completare automaticamente le disponibilità scadute.
 *
 * Questo comando viene eseguito automaticamente ogni giorno e imposta lo stato
 * COMPLETED per tutte le disponibilità il cui giorno della cena è passato.
 *
 * Logica:
 * - Trova tutte le disponibilità con dinner_date.date < oggi
 * - Che sono in stato AVAILABLE_TO_HOST, ALMOST_FULL, o FULL
 * - Le passa allo stato COMPLETED
 */
class CompleteExpiredAvailabilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'availabilities:complete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completa automaticamente le disponibilità il cui giorno della cena è passato';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $yesterday = Carbon::yesterday()->endOfDay();

        // Trova tutte le disponibilità con data passata che non sono già completate o cancellate
        $availabilities = DinnerAvailability::whereHas('dinnerDate', function ($query) use ($yesterday) {
            $query->where('dinner_date', '<', $yesterday);
        })
            ->where('can_host', true) // Solo gli host possono essere completati
            ->whereIn('status', [
                DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
                DinnerAvailabilityStatus::ALMOST_FULL,
                DinnerAvailabilityStatus::FULL,
            ])
            ->get();

        if ($availabilities->isEmpty()) {
            $this->info('Nessuna disponibilità da completare.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($availabilities as $availability) {
            $availability->status = DinnerAvailabilityStatus::COMPLETED;
            $availability->save();
            $count++;

            $this->info("Completata disponibilità ID {$availability->id} per {$availability->user->name} del {$availability->dinnerDate->dinner_date->format('d/m/Y')}");
        }

        $this->info("Totale disponibilità completate: {$count}");

        return self::SUCCESS;
    }
}
