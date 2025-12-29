<?php

namespace App\Filament\App\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget per statistiche prossime cene dell'utente.
 *
 * Mostra 4 card con informazioni chiave:
 * - Prossima cena come HOST (data e prenotazioni/capacità)
 * - Prossime cene come GUEST (numero cene prenotate)
 * - Richieste pendenti da confermare (se HOST)
 * - Membri del gruppo
 *
 * @see NextDinnersStatsWidget
 */
class NextDinnersStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    /**
     * Costruisce le statistiche da mostrare nel widget.
     *
     * Calcola dinamicamente:
     * - Prossima disponibilità come host con conteggio prenotazioni
     * - Numero prossime prenotazioni come guest
     * - Richieste di prenotazione in attesa di conferma
     * - Totale membri del gruppo
     *
     * @return array Array di Stat card per il widget
     */
    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $group = $user->dinnerGroup;

        if ( ! $group) {
            return [
                Stat::make('Gruppo', 'Non fai parte di un gruppo')
                    ->description('Unisciti o crea un gruppo per iniziare')
                    ->icon('tabler-users-group')
                    ->color('warning'),
            ];
        }

        // 1. Prossima cena come HOST
        $nextHostDinner = $user->availabilities()
            ->where('can_host', true)
            ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_date', '>=', now()->toDateString()))
            ->with(['dinnerDate', 'confirmedBookings'])
            ->orderBy('id')
            ->first();

        $hostStat = $this->getHostStat($nextHostDinner);

        // 2. Prossime cene come GUEST
        $upcomingGuestBookings = $user->guestBookings()
            ->where('status', 'confirmed')
            ->whereHas('hostAvailability.dinnerDate', fn ($q) => $q->where('dinner_date', '>=', now()->toDateString()))
            ->count();

        $guestStat = Stat::make('Prossime cene come ospite', $upcomingGuestBookings)
            ->description($upcomingGuestBookings > 0 ? 'Cene confermate in arrivo' : 'Nessuna cena prenotata')
            ->icon('tabler-tools-kitchen-3')
            ->color($upcomingGuestBookings > 0 ? 'success' : 'gray');

        // 3. Richieste pendenti (se HOST)
        $pendingRequests = \App\Models\DinnerBooking::query()
            ->whereHas('hostAvailability', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'pending')
            ->count();

        $pendingStat = Stat::make('Richieste da confermare', $pendingRequests)
            ->description($pendingRequests > 0 ? 'Prenotazioni in attesa' : 'Nessuna richiesta')
            ->icon('tabler-clock-check')
            ->color($pendingRequests > 0 ? 'warning' : 'gray');

        // 4. Membri gruppo
        $membersStat = Stat::make('Membri gruppo', $group->members()->count())
            ->description($group->name)
            ->icon('tabler-users')
            ->color('info');

        return [
            $hostStat,
            $guestStat,
            $pendingStat,
            $membersStat,
        ];
    }

    /**
     * Costruisce la statistica per la prossima cena come HOST.
     *
     * Mostra data della cena e rapporto prenotazioni/capacità.
     * Se non ci sono cene future come host, mostra messaggio appropriato.
     *
     * @param  \App\Models\DinnerAvailability|null  $nextHostDinner  Prossima disponibilità host
     * @return Stat Card statistica per prossima cena host
     */
    protected function getHostStat($nextHostDinner): Stat
    {
        if ( ! $nextHostDinner) {
            return Stat::make('Prossima cena come host', 'Nessuna')
                ->description('Dichiara disponibilità per ospitare')
                ->icon('tabler-chef-hat')
                ->color('gray');
        }

        $date      = Carbon::parse($nextHostDinner->dinnerDate->dinner_date);
        $confirmed = $nextHostDinner->confirmedBookings->sum('guests_count');
        $capacity  = $nextHostDinner->max_guests;

        return Stat::make('Prossima cena come host', $date->isoFormat('D MMM'))
            ->description("{$confirmed}/{$capacity} ospiti confermati")
            ->icon('tabler-chef-hat-filled')
            ->color('success');
    }
}
