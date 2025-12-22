<?php

namespace App\Filament\App\Resources\DinnerBookings\Schemas;

use Closure;
use Filament\Schemas\Schema;
use App\Enums\DinnerBookingStatus;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Flex;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

/**
 * Form schema per modificare una prenotazione esistente.
 *
 * Campi modificabili:
 * - Numero di ospiti (con validazione capacità)
 * - Items portati
 * - Note
 * - Stato (pending/confirmed/cancelled)
 *
 * NON modificabile:
 * - Host e data (la prenotazione va cancellata e ricreata)
 */
class DinnerBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([
                Flex::make([
                    // !
                    Section::make('Modifica Prenotazione')
                        ->schema([
                            // !
                            Select::make('status')
                                ->label('Stato prenotazione')
                                ->options(DinnerBookingStatus::class)
                                ->helperText('Conferma la tua presenza o annulla la prenotazione')
                                ->required(),
                            // !
                            TextInput::make('guests_count')
                                ->label('Numero di ospiti')
                                ->helperText('Quante persone verranno (incluso te)')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->rules([
                                    function ($livewire) {
                                        return function (string $attribute, $value, Closure $fail) use ($livewire) {
                                            if ( ! $livewire->record) {
                                                return; // Solo in edit
                                            }

                                            $hostAvailability = $livewire->record->hostAvailability;

                                            if ( ! $hostAvailability) {
                                                $fail('Host non trovato.');

                                                return;
                                            }

                                            // Calcola prenotazioni degli altri (escludi la tua)
                                            $otherBookingsCount = $hostAvailability->confirmedBookings()
                                                ->where('id', '!=', $livewire->record->id)
                                                ->sum('guests_count');

                                            $availableSpots = $hostAvailability->max_guests - $otherBookingsCount;

                                            if ($value > $availableSpots) {
                                                $fail("Posti disponibili: {$availableSpots}. Non puoi prenotare per {$value} ospiti.");
                                            }
                                        };
                                    },
                                ]),
                            // !
                            TagsInput::make('bringing_items')
                                ->label('Cosa porti')
                                ->placeholder('Aggiungi items (premi Invio dopo ogni item)')
                                ->helperText('Es: Vino, Dolce, Antipasto, etc.')
                                ->suggestions(['Vino', 'Dolce', 'Antipasto', 'Frutta', 'Pane', 'Acqua', 'Caffè']),
                            // !
                            Textarea::make('notes')
                                ->label('Note aggiuntive')
                                ->placeholder('Eventuali note, intolleranze alimentari, orario di arrivo previsto...')
                                ->rows(3),

                        ])
                        ->columnSpanFull()
                        ->description('Puoi modificare il numero di ospiti, cosa porti e le note.')
                        ->columns(2),
                    // !
                    Section::make('Informazioni Prenotazione')

                        ->relationship('hostAvailability')
                        ->schema([
                            TextEntry::make('status')->label('Stato')->badge(),
                            TextEntry::make('user.name')->label('Host')->color('info')->icon('tabler-chef-hat')->iconColor('info'),
                            TextEntry::make('user.profile.full_address')->label('Indirizzo')->color('info')->icon('tabler-map-route')->iconColor('info'),
                            TextEntry::make('max_guests')->label('Numero massimo di ospiti')->badge()->color('info')->icon('tabler-users')->iconColor('info'),
                            TextEntry::make('dinnerDate.dinner_date')->label('Data Evento')->dateTime('d/m/Y')->color('info')->icon('tabler-users')->iconColor('info'),
                            TextEntry::make('note')->label('Note')->color('info')->icon('tabler-note')->iconColor('info')->columnSpanFull(),
                        ])->columns(2)->grow(true),

                ])->columnSpanFull(),

            ]);
    }
}
