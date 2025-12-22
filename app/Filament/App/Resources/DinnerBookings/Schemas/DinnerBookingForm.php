<?php

namespace App\Filament\App\Resources\DinnerBookings\Schemas;

use Filament\Schemas\Schema;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Enums\DinnerAvailabilityStatus;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class DinnerBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Prenotazione')
                    ->schema([
                        // Seleziona host disponibile
                        Select::make('host_availability_id')
                            ->label('Dove vuoi andare a cena')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return DinnerAvailability::where('can_host', true)
                                    ->where('status', DinnerAvailabilityStatus::AVAILABLE_TO_HOST)
                                    ->whereHas('dinnerDate', function ($q) {
                                        $q->where('dinner_group_id', Auth::user()->dinner_group_id)
                                            ->where('dinner_date', '>=', now());
                                    })
                                    ->with(['user', 'dinnerDate'])
                                    ->get()
                                    ->mapWithKeys(function ($availability) {
                                        $date  = $availability->dinnerDate->dinner_date->format('d/m/Y');
                                        $host  = $availability->user->nome . ' ' . $availability->user->cognome;
                                        $spots = $availability->available_spots;

                                        return [$availability->id => "{$date} - {$host} ({$spots} posti disponibili)"];
                                    });
                            })
                            ->helperText('Scegli l\'host e la data per la tua prenotazione')
                            ->disabled(fn ($context) => $context === 'edit'),

                        TextInput::make('guests_count')
                            ->label('Numero di ospiti')
                            ->helperText('Quante persone verranno oltre a te')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(1),

                        TagsInput::make('bringing_items')
                            ->label('Cosa porti')
                            ->placeholder('Aggiungi items (premi Invio dopo ogni item)')
                            ->helperText('Es: Vino, Dolce, Antipasto, etc.')
                            ->suggestions(['Vino', 'Dolce', 'Antipasto', 'Frutta', 'Pane', 'Acqua', 'Caffè'])
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Note aggiuntive')
                            ->placeholder('Eventuali note, intolleranze alimentari, orario di arrivo previsto...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Stato')
                            ->options(DinnerBookingStatus::class)
                            ->default(DinnerBookingStatus::PENDING)
                            ->helperText('Conferma la prenotazione o annullala')
                            ->required()
                            ->disabled(fn ($context) => $context === 'create'), // Solo edit può cambiare stato
                    ])
                    ->columns(2),
            ]);
    }
}
