<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Schemas;

use Closure;
use Carbon\Carbon;
use App\Models\DinnerDate;
use Filament\Schemas\Schema;
use App\Enums\CancellationReason;
use App\Models\DinnerAvailability;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use App\Enums\DinnerAvailabilityStatus;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class DinnerAvailabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ! Sezione Data
                Section::make('Quando?')
                    ->description('Seleziona il giorno per cui vuoi dichiarare la tua disponibilità')
                    ->icon('tabler-calendar-event')
                    ->iconColor('primary')
                    ->schema([
                        DatePicker::make('dinnerDate.dinner_date')
                            ->label('Giorno della cena')
                            ->date()
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->native(false)
                            ->minDate(function ($context) {
                                if ($context == 'create') {
                                    return Carbon::now()->format('Y-m-d');
                                }
                            })
                            ->formatStateUsing(function ($record) {
                                return $record?->dinnerDate
                                    ->dinner_date?->format('Y-m-d')
                                    ?? Carbon::now()->toDateString();
                            })
                            ->rules([
                                function ($livewire) {
                                    return function (string $attribute, $value, Closure $fail) use ($livewire) {
                                        $dateSearch = Carbon::parse($value)->format('Y-m-d');
                                        $dinnerDate = DinnerDate::where('dinner_group_id', Auth::user()->dinner_group_id)
                                            ->where('dinner_date', $dateSearch)
                                            ->first();

                                        if ( ! $dinnerDate) {
                                            return true;
                                        }

                                        // Verifica se esiste già una disponibilità per questo utente e questa data
                                        $query = DinnerAvailability::query()
                                            ->where('dinner_date_id', $dinnerDate->id)
                                            ->where('user_id', Auth::user()->id)
                                            ->when($livewire->record, function ($query) use ($livewire) {
                                                return $query->where('id', '!=', $livewire->record->id);
                                            });
                                        $exists = $query->exists();

                                        if ($exists) {
                                            $fail('Hai già dichiarato la tua disponibilità per questo giorno.');
                                        }
                                    };
                                },
                            ])
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columns(1),

                // ! Sezione Ruolo
                Section::make('Come parteciperai?')
                    ->description('Scegli se ospitare la cena o partecipare come ospite')
                    ->icon('tabler-users')
                    ->iconColor('primary')
                    ->schema([
                        ToggleButtons::make('can_host')
                            ->label('Ruolo')
                            ->default(false)
                            ->boolean()
                            ->colors([
                                true  => 'success',
                                false => 'info',
                            ])
                            ->icons([
                                true  => 'tabler-chef-hat',
                                false => 'tabler-tools-kitchen-3',
                            ])
                            ->inline()
                            ->grouped()
                            ->live()
                            ->disabledOn('edit')
                            ->afterStateUpdated(function (Set $set, $state) {
                                // Quando can_host cambia, aggiorna campi correlati (ma NON status)
                                if ($state) {
                                    // Se diventa host, usa max_guests del profilo
                                    $userMaxGuests = Auth::user()->profile?->max_guests ?? 1;
                                    $set('max_guests', $userMaxGuests);
                                } else {
                                    // Se diventa guest, resetta campi host
                                    $set('max_guests', null);
                                    $set('dinner_name', null);
                                }
                            })
                            ->options([
                                false => 'Partecipo come ospite',
                                true  => 'Ospito io la cena',
                            ])
                            ->required()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Stato disponibilità')
                            ->default(function (Get $get, $context) {
                                // Imposta default solo in creazione
                                if ($context !== 'create') {
                                    return;
                                }

                                $canHost = $get('can_host');

                                // Default in base a can_host
                                return $canHost
                                    ? DinnerAvailabilityStatus::AVAILABLE_TO_HOST->value
                                    : DinnerAvailabilityStatus::AVAILABLE->value;
                            })
                            ->options(function (Get $get, $context) {
                                $canHost = $get('can_host');

                                // Filtra gli stati in base a can_host
                                $allStatuses = DinnerAvailabilityStatus::cases();

                                $filtered = collect($allStatuses)->filter(function ($status) use ($canHost, $context) {
                                    if ($canHost) {
                                        return $context == 'create' ? $status == DinnerAvailabilityStatus::AVAILABLE_TO_HOST : $status->isHostStatus();
                                    } else {
                                        return $context == 'create' ? $status == DinnerAvailabilityStatus::AVAILABLE : $status->isGuestStatus();
                                    }
                                });

                                return $filtered->pluck('value', 'value')
                                    ->map(fn ($value) => DinnerAvailabilityStatus::from($value)->getLabel())
                                    ->toArray();
                            })
                            ->selectablePlaceholder(false)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columns(1),

                // ! Sezione Note (sempre visibile)
                Section::make('Note aggiuntive')
                    ->description('Aggiungi eventuali note o informazioni utili')
                    ->icon('tabler-notes')
                    ->iconColor('primary')

                    ->schema([
                        Textarea::make('note')
                            ->hiddenLabel()
                            ->placeholder('es. Allergie, preferenze alimentari, dettagli sulla location...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])

                    ->columns(1),

                // ! Sezione Dettagli Host (visibile solo se can_host = true)
                Section::make('Dettagli della cena')
                    ->description('Personalizza i dettagli della tua cena')
                    ->icon('tabler-chef-hat')
                    ->iconColor('primary')

                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('dinner_name')
                                    ->label('Nome della cena')
                                    ->maxLength(255)
                                    ->placeholder('es. Pizza napoletana, Pasta al forno, Sushi night...')
                                    ->helperText('Dai un nome invitante alla tua cena!')
                                    ->columnSpan(1),

                                TextInput::make('max_guests')
                                    ->label('Posti disponibili')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(50)
                                    ->suffix('ospiti')
                                    ->prefixIcon('tabler-users')
                                    ->default(fn () => Auth::user()->profile?->max_guests ?? 1)
                                    ->required()
                                    ->dehydrated()
                                    ->helperText('Quanti ospiti puoi accogliere?')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->visible(fn (Get $get) => $get('can_host') === true)
                    ->collapsible()
                    ->columns(1),

                // ! Sezione Cancellazione (visibile solo se HOST_CANCELLED)
                Section::make('Motivo cancellazione')
                    ->description('Specifica perché stai cancellando questa cena')
                    ->icon('tabler-calendar-x')
                    ->iconColor('primary')

                    ->schema([
                        Select::make('cancellation_reason')
                            ->label('Motivo')
                            ->options(CancellationReason::class)
                            ->required()
                            ->helperText('Aiuta il gruppo a capire il motivo della cancellazione')
                            ->columnSpanFull(),
                    ])
                    ->visible(
                        fn (Get $get) => $get('can_host') === true &&
                            $get('status') === DinnerAvailabilityStatus::HOST_CANCELLED->value
                    )
                    ->collapsible()
                    ->columns(1),

            ]);
    }
}
