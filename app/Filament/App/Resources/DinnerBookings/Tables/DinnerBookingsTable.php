<?php

namespace App\Filament\App\Resources\DinnerBookings\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use App\Enums\DinnerBookingStatus;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

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
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(DinnerBookingStatus::class)
                    ->native(false),

                SelectFilter::make('dinner_date')
                    ->label('Data')
                    ->relationship('hostAvailability.dinnerDate', 'dinner_date')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('hostAvailability.dinnerDate.dinner_date', 'desc')
            ->recordActions([
                // ! Azione per confermare la prenotazione
                Action::make('confirm')
                    ->label('Conferma')

                    ->icon('tabler-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === DinnerBookingStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Conferma la tua prenotazione')
                    ->modalDescription('Confermi la tua presenza?')
                    ->action(function ($record) {
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
                    ->label('Annulla')
                    ->icon('tabler-x')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== DinnerBookingStatus::CANCELLED)
                    ->requiresConfirmation()
                    ->modalHeading('Annulla prenotazione')
                    ->modalDescription('Sei sicuro di voler annullare questa prenotazione?')
                    ->action(function ($record) {
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
