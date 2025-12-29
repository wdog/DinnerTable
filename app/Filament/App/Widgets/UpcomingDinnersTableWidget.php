<?php

namespace App\Filament\App\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\TableWidget as BaseWidget;

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
            ->query(
                DinnerAvailability::query()
                    ->where('user_id', Auth::id())
                    ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_date', '>=', now()->toDateString()))
                    ->with(['dinnerDate', 'confirmedBookings'])
                    ->orderBy('id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('dinnerDate.dinner_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->description(fn ($record) => Carbon::parse($record->dinnerDate->dinner_date)->isoFormat('dddd'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Ruolo')
                    ->badge()
                    ->state(fn ($record) => $record->can_host ? 'Host' : 'Guest')
                    ->color(fn ($record) => $record->can_host ? 'success' : 'info')
                    ->icon(fn ($record) => $record->can_host ? 'tabler-chef-hat' : 'tabler-tools-kitchen-3'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                 ,

                Tables\Columns\TextColumn::make('guests_info')
                    ->label('Ospiti')
                    ->state(function ($record) {
                        if ( ! $record->can_host) {
                            return '-';
                        }

                        $confirmed = $record->confirmedBookings->sum('guests_count');
                        $pending = $record->bookings()->where('status', 'pending')->sum('guests_count');
                        $total = $record->max_guests;

                        return "{$confirmed} / {$total}";
                    })
                    ->description(function ($record) {
                        if ( ! $record->can_host) {
                            return null;
                        }

                        $confirmed = $record->confirmedBookings->sum('guests_count');
                        $pending = $record->bookings()->where('status', 'pending')->sum('guests_count');
                        $available = $record->available_spots;

                        $details = [];
                        if ($confirmed > 0) {
                            $details[] = "✓ {$confirmed} confermati";
                        }
                        if ($pending > 0) {
                            $details[] = "⏳ {$pending} in attesa";
                        }
                        if ($available > 0) {
                            $details[] = "🔓 {$available} liberi";
                        }

                        return implode(' • ', $details) ?: 'Nessuna prenotazione';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ( ! $record->can_host) {
                            return 'gray';
                        }

                        $confirmed = $record->confirmedBookings->sum('guests_count');
                        $total = $record->max_guests;

                        if ($confirmed === 0) {
                            return 'gray';
                        }
                        if ($confirmed === $total) {
                            return 'success';
                        }

                        return 'warning';
                    })
                    ->icon('tabler-users'),
            ])
            ->heading('Le tue prossime cene')
            ->paginated([5,10,25])
            ->defaultPaginationPageOption(5);
    }
}
