<?php

namespace App\Filament\App\Resources\DinnerBookings\Tables;

use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use App\Enums\DinnerBookingStatus;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class DinnerBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostAvailability.dinnerDate.dinner_date')
                    ->label('Data')
                    ->date('l - d F Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('hostAvailability.user.name')
                    ->label('Host')
                    ->sortable(),

                TextColumn::make('hostAvailability.user.profile.city')
                    ->label('Città')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('hostAvailability.user.profile.address')
                    ->label('Indirizzo')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('hostAvailability.user.profile.house_number')
                    ->label('Civico')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('guests_count')
                    ->label('N. Ospiti')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('bringing_items')
                    ->label('Porto')
                    ->badge()
                    ->separator(',')
                    ->default('Niente')
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Prenotato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(DinnerBookingStatus::class)
                    ->native(false),

                Filter::make('dinner_date')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('from')
                            ->label('Da')
                            ->native(false),
                        DatePicker::make('until')
                            ->label('A')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereHas('hostAvailability.dinnerDate', function (Builder $query) use ($date) {
                                    $query->whereDate('dinner_date', '>=', $date);
                                })
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereHas('hostAvailability.dinnerDate', function (Builder $query) use ($date) {
                                    $query->whereDate('dinner_date', '<=', $date);
                                })
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = 'Da: ' . Carbon::parse($data['from'])->format('d/m/Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'A: ' . Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

            ], FiltersLayout::AboveContent)
            ->defaultSort('hostAvailability.dinnerDate.dinner_date', 'desc')
            ->recordActions([
                // ! Azione per confermare la prenotazione
                Action::make('confirm')
                    ->label('Conferma')
                    ->icon('tabler-check')
                    ->color('success')
                    ->size(Size::ExtraSmall)
                    ->visible(
                        function ($record): bool {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();

                            return $record->status !== DinnerBookingStatus::CONFIRMED &&
                                $user->can('update', $record);
                        }
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Conferma la tua prenotazione')
                    ->modalDescription('Confermi la tua presenza?')
                    ->action(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        // Verifica autorizzazione tramite policy prima di salvare
                        if ( ! $user->can('update', $record)) {
                            Notification::make()
                                ->danger()
                                ->title('Azione non permessa')
                                ->body('Non puoi modificare questa prenotazione (cena completata o cancellata).')
                                ->send();

                            return;
                        }

                        $record->status = DinnerBookingStatus::CONFIRMED;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Prenotazione confermata')
                            ->body('La tua presenza è stata confermata!')
                            ->send();
                    }),

                // ! Azione per annullare la prenotazione
                Action::make('cancel')
                    ->label('Cancella')
                    ->icon('tabler-x')
                    ->color('danger')
                    ->size(Size::ExtraSmall)
                    ->visible(
                        function ($record): bool {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();

                            return $record->status !== DinnerBookingStatus::CANCELLED &&
                                $user->can('update', $record);
                        }
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Annulla prenotazione')
                    ->modalDescription('Sei sicuro di voler annullare questa prenotazione?')
                    ->action(function ($record) {
                        // Verifica autorizzazione tramite policy prima di salvare
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        if ( ! $user->can('update', $record)) {
                            Notification::make()
                                ->danger()
                                ->title('Azione non permessa')
                                ->body('Non puoi modificare questa prenotazione (cena completata o cancellata).')
                                ->send();

                            return;
                        }

                        $record->status = DinnerBookingStatus::CANCELLED;
                        $record->save();

                        Notification::make()
                            ->warning()
                            ->title('Prenotazione annullata')
                            ->body('La prenotazione è stata annullata.')
                            ->send();
                    }),

                // ! edit
                EditAction::make()
                    ->label('Modifica'),
                // ! delete
                DeleteAction::make()
                    ->label('Elimina'),
            ])

            ->emptyStateHeading('Nessuna prenotazione')
            ->emptyStateDescription('Non hai ancora effettuato prenotazioni. Crea la tua prima prenotazione!')
            ->emptyStateIcon('tabler-calendar-off');
    }
}
