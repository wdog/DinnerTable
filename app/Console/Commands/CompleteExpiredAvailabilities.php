<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Enums\DinnerBookingStatus;
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
     * Esegue il comando per completare le disponibilità scadute.
     *
     * Il comando esegue le seguenti operazioni:
     *
     * 1. **Identificazione disponibilità scadute**:
     *    - Trova tutte le disponibilità con dinner_date < oggi (00:00)
     *    - Filtra solo quelle di tipo host (can_host = true)
     *    - Considera solo stati attivi: AVAILABLE_TO_HOST, ALMOST_FULL, FULL
     *
     * 2. **Aggiornamento stato disponibilità**:
     *    - Imposta status a COMPLETED per marcarle come concluse
     *    - Rende immutabile lo storico (non più modificabili via Policy)
     *
     * 3. **Gestione prenotazioni non confermate**:
     *    - Cancella tutte le prenotazioni pending/non confermate
     *    - Mantiene le prenotazioni confirmed nello stato originale
     *    - Preserva lo storico di chi ha partecipato
     *
     * 4. **Output e logging**:
     *    - Mostra messaggio per ogni disponibilità completata
     *    - Conta e mostra il totale delle operazioni
     *
     * Scheduling:
     * Questo comando dovrebbe essere schedulato per eseguire quotidianamente,
     * tipicamente dopo la mezzanotte (es. 00:30).
     *
     * Esempio scheduling in routes/console.php:
     * ```php
     * Schedule::command('availabilities:complete-expired')->dailyAt('00:30');
     * ```
     *
     * @return int Exit code (SUCCESS o FAILURE)
     */
    public function handle(): int
    {
        $today = Carbon::today()->startOfDay();

        // Trova tutte le disponibilità con data passata (< oggi alle 00:00)
        // Es: cena di lunedì viene completata martedì alle 00:00
        $availabilities = DinnerAvailability::whereHas('dinnerDate', function ($query) use ($today) {
            $query->where('dinner_date', '<', $today);
        })
            ->where('can_host', true) // Solo gli host possono essere completati
            ->whereIn('status', [
                DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
                DinnerAvailabilityStatus::ALMOST_FULL,
                DinnerAvailabilityStatus::FULL,
                // DinnerAvailabilityStatus::COMPLETED,
            ])
            ->get();

        if ($availabilities->isEmpty()) {
            $this->info('Nessuna disponibilità da completare.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($availabilities as $availability) {
            $availability->status = DinnerAvailabilityStatus::COMPLETED;
            $availability->bookings()->notConfirmed()->update(['status' => DinnerBookingStatus::CANCELLED]);
            $availability->save();
            $count++;

            $this->info("Completata disponibilità ID {$availability->id} per {$availability->user->name} del {$availability->dinnerDate->dinner_date->format('d/m/Y')}");
        }

        $this->info("Totale disponibilità completate: {$count}");

        return self::SUCCESS;
    }
}
