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
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
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

                        Select::make('status')
                            ->default(DinnerAvailabilityStatus::AVAILABLE)
                            ->options(DinnerAvailabilityStatus::class)
                            ->required(),

                        ToggleButtons::make('can_host')
                            ->default(false)
                            ->boolean()
                            ->colors([
                                true => 'primary',
                                false => 'info',
                            ])
                            ->icons([
                                true => 'tabler-chef-hat',
                                false => 'tabler-pacman',
                            ])
                            ->inline()
                            ->grouped()
                            ->label('Ospito io la cena')
                            ->required(),
                        Textarea::make('note')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
