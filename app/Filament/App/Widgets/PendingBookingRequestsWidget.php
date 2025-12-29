<?php

namespace App\Filament\App\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\DinnerBooking;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Widget per richieste di prenotazione pendenti.
 *
 * Mostra solo se l'utente ha disponibilità come HOST con
 * prenotazioni in stato PENDING. Permette conferma/rifiuto inline.
 */
class PendingBookingRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DinnerBooking::query()
                    ->whereHas('hostAvailability', fn($q) => $q->where('user_id', Auth::id()))
                    ->where('status', 'pending')
                    ->with(['guest', 'hostAvailability.dinnerDate'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('hostAvailability.dinnerDate.dinner_date')
                    ->label('Data cena')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guest.name')
                    ->label('Ospite')
                    ->searchable(),

                Tables\Columns\TextColumn::make('guests_count')
                    ->label('N. Persone')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('bringing_items')
                    ->label('Porta')
                    ->badge()
                    ->state(fn($record) => $record->bringing_items ?? [])
                    ->default('-'),

                TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Richiesta il')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Conferma')
                    ->icon('tabler-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->authorize('update')
                    ->action(function (DinnerBooking $record) {
                        $record->update(['status' => 'confirmed']);

                        Notification::make()
                            ->success()
                            ->title('Prenotazione confermata')
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Rifiuta')
                    ->icon('tabler-x')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize('updateGuestBooking')
                    ->action(function (DinnerBooking $record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->warning()
                            ->title('Prenotazione rifiutata')
                            ->send();
                    }),
            ])
            ->heading('Richieste di prenotazione da confermare')
            ->emptyStateHeading('Nessuna richiesta pendente')
            ->emptyStateDescription('Quando riceverai prenotazioni, appariranno qui')
            ->emptyStateIcon('tabler-inbox')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
