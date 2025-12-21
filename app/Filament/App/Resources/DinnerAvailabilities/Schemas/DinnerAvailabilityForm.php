<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Schemas;

use App\Enums\DinnerAvailabilityStatus;
use App\Models\DinnerAvailability;
use App\Models\DinnerDate;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DinnerAvailabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->columns(3)
                    ->schema([
                        // !
                        DatePicker::make('dinnerDate.dinner_date')
                            ->label('Giorno')
                            ->date()
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->native(false)
                            ->minDate(function ($context) {
                                if ($context == 'create') {
                                    return Carbon::now()->format('Y-m-d');
                                }

                                return null;
                            })

                            ->formatStateUsing(function ($record) {
                                return $record?->dinnerDate
                                    ->dinner_date?->format('Y-m-d')
                                    ?? Carbon::now()->toDateString();
                            })

                            ->rules([
                                function ($livewire) {
                                    return function ($attribute, $value, Closure $fail) use ($livewire) {
                                        $dateSearch = Carbon::parse($value)->format('Y-m-d');
                                        $dinnerDate = DinnerDate::where('dinner_group_id', Auth::user()->dinner_group_id)
                                            ->where('dinner_date', $dateSearch)
                                            ->first();

                                        if (! $dinnerDate) {
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
                            ->required(),

                        // !
                        ToggleButtons::make('can_host')
                            ->default(false)
                            ->boolean()
                            ->colors([
                                true => 'primary',
                                false => 'info',
                            ])
                            ->icons([
                                true => 'tabler-chef-hat',
                                false => 'tabler-tools-kitchen-3',
                            ])
                            ->inline()
                            ->grouped()
                            ->label('Ospito io la cena')
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                // Quando can_host cambia, imposta uno status valido
                                if ($state) {
                                    // Se diventa host, imposta AVAILABLE_TO_HOST
                                    $set('status', DinnerAvailabilityStatus::AVAILABLE_TO_HOST->value);
                                    // Usa max_guests del profilo utente come default
                                    $userMaxGuests = Auth::user()->profile?->max_guests ?? 1;
                                    $set('max_guests', $userMaxGuests);
                                } else {
                                    // Se diventa guest, imposta AVAILABLE
                                    $set('status', DinnerAvailabilityStatus::AVAILABLE->value);
                                    // IMPORTANTE: Resetta max_guests quando non può ospitare
                                    $set('max_guests', null);
                                }
                            })
                            ->required(),

                        // !
                        Select::make('status')
                            ->default(DinnerAvailabilityStatus::AVAILABLE)
                            ->options(function (Get $get) {
                                $canHost = $get('can_host');

                                // Filtra gli stati in base a can_host
                                $allStatuses = DinnerAvailabilityStatus::cases();

                                $filtered = collect($allStatuses)->filter(function ($status) use ($canHost) {
                                    if ($canHost) {
                                        return $status->isHostStatus();
                                    } else {
                                        return $status->isGuestStatus();
                                    }
                                });

                                return $filtered->pluck('value', 'value')
                                    ->map(fn ($value) => DinnerAvailabilityStatus::from($value)->getLabel())
                                    ->toArray();
                            })
                            ->live()
                            ->required(),

                        // Campo max_guests visibile solo quando can_host è true
                        TextInput::make('max_guests')
                            ->label('Numero massimo ospiti')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(fn () => Auth::user()->profile?->max_guests ?? 1)
                            ->visible(fn (Get $get) => $get('can_host') === true)
                            ->required(fn (Get $get) => $get('can_host') === true)
                            ->dehydrated() // IMPORTANTE: assicura che il campo venga sempre processato anche quando nascosto
                            ->hint('Valore di default dal tuo profilo'),

                        Textarea::make('note')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
