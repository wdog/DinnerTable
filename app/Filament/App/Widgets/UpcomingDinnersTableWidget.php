<?php

namespace App\Filament\App\Widgets;

use Carbon\Carbon;
use Filament\Tables\Table;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;

/**
 * Widget tabella prossime cene dell'utente.
 *
 * Mostra le prossime 7 cene in cui l'utente è coinvolto
 * sia come HOST che come GUEST.
 */
class UpcomingDinnersTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (DinnerAvailability $record) => DinnerAvailabilityResource::getUrl('edit', ['record' => $record]))
            ->query(
                DinnerAvailability::query()
                    ->where('user_id', Auth::id())
                    ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_date', '>=', now()->toDateString()))
                    ->with(['dinnerDate', 'confirmedBookings'])
                    ->orderBy('id')
            )
            ->columns([
                TextColumn::make('dinnerDate.dinner_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->description(fn ($record) => Carbon::parse($record->dinnerDate->dinner_date)->isoFormat('dddd'))
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Ruolo')
                    ->badge()
                    ->state(fn ($record) => $record->can_host ? 'Host' : 'Guest')
                    ->color(fn ($record) => $record->can_host ? 'success' : 'info')
                    ->icon(fn ($record) => $record->can_host ? 'tabler-chef-hat' : 'tabler-tools-kitchen-3'),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge(),

                TextColumn::make('guests_info')
                    ->label('Ospiti')
                    ->state(function ($record) {
                        if ( ! $record->can_host) {
                            return '-';
                        }

                        $confirmed = $record->confirmedBookings->sum('guests_count');
                        $pending   = $record->bookings()->where('status', 'pending')->sum('guests_count');
                        $total     = $record->max_guests;

                        return "{$confirmed} / {$total}";
                    })
                    ->description(function ($record) {
                        if ( ! $record->can_host) {
                            return;
                        }

                        $confirmed = $record->confirmedBookings->sum('guests_count');
                        $pending   = $record->bookings()->where('status', 'pending')->sum('guests_count');
                        $available = $record->available_spots;

                        $details = [];
                        if ($confirmed > 0) {
                            $details[] = "Confermati: {$confirmed}";
                        }
                        if ($pending > 0) {
                            $details[] = "In attesa: {$pending}";
                        }
                        if ($available > 0) {
                            $details[] = "Liberi: {$available}";
                        }

                        return $details ? implode(' • ', $details) : 'Nessuna prenotazione';
                    })
                    ->icon(fn ($record) => $record->can_host ? match (true) {
                        $record->confirmedBookings->sum('guests_count') === $record->max_guests => 'tabler-glass-full',
                        $record->bookings()->where('status', 'pending')->exists()               => 'tabler-user-question',
                        $record->confirmedBookings->sum('guests_count') === 0                   => 'tabler-sparkles',
                        default                                                                 => 'tabler-users',
                    } : null)
                    ->badge()
                    ->color(function ($record) {
                        if ( ! $record->can_host) {
                            return 'gray';
                        }

                        $confirmed = $record->confirmedBookings->sum('guests_count');
                        $total     = $record->max_guests;

                        if ($confirmed === 0) {
                            return 'gray';
                        }
                        if ($confirmed === $total) {
                            return 'success';
                        }

                        return 'warning';
                    }),
            ])
            ->heading('Le tue prossime cene')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
