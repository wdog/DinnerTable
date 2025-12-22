<?php

namespace App\Filament\App\Resources\DinnerBookings\Schemas;

use Closure;
use Filament\Schemas\Schema;
use App\Enums\DinnerBookingStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

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
                Section::make('Modifica Prenotazione')
                    ->columnSpanFull()
                    ->description('Puoi modificare il numero di ospiti, cosa porti e le note.')
                    ->columns([
                        'sm' => 3,
                        'lg' => 4,
                    ])
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
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}
