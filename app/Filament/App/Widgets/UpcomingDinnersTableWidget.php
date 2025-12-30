<?php

namespace App\Filament\App\Widgets;

use Filament\Tables\Table;
use App\Models\DinnerAvailability;
use Illuminate\Support\HtmlString;
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
            )->columns([
                TextColumn::make('dinnerDate.dinner_date')
                    ->label('Data')
                    ->date('d M Y, l')
                    ->description(
                        function ($record) {
                            return new HtmlString("<div class='text-primary-500 font-bold'>{$record->dinner_name}</div>");
                        }
                    )
                    ->html()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Ruolo')
                    ->badge()
                    ->state(fn ($record) => $record->can_host ? 'Host' : 'Guest')
                    ->color(fn ($record) => $record->can_host ? 'success' : 'info')
                    ->icon(fn ($record) => $record->can_host ? 'tabler-chef-hat' : 'tabler-tools-kitchen-3')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                        $available = $record->available_spots;
                        $pending   = $record->bookings()->where('status', 'pending')->sum('guests_count');

                        $details = [];
                        // Icona check + numero ospiti confermati
                        if ($confirmed > 0) {
                            $details[] = svg('tabler-check', 'w-4 h-4 inline text-primary-500')->toHtml() . "<span class='text-gray-600 dark:text-gray-200 p-2 font-bold'>{$confirmed}</span>";
                        }
                        // Icona clock + numero ospiti in attesa
                        if ($pending > 0) {
                            $details[] = svg('tabler-clock', 'w-4 h-4 inline text-warning-500')->toHtml() . "<span class='text-gray-600 dark:text-gray-200 p-2 font-bold'>{$pending}</span>";
                        }
                        // Icona user-plus + posti ancora disponibili
                        if ($available > 0) {
                            $details[] = svg('tabler-user-plus', 'w-4 h-4 inline text-info-500')->toHtml() . "<span class='text-gray-600 dark:text-gray-200 p-2 font-bold'>{$available}</span>";
                        }

                        return $details ? new HtmlString(implode(' ', $details)) : 'Nessuna prenotazione';
                    })
                    ->html()
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
