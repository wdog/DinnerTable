{{--
    Calendario Disponibilità di Gruppo - Vista Settimanale

    Questa vista mostra le disponibilità del gruppo per una settimana specifica.

    Funzionalità principali:
    - Navigazione settimana per settimana
    - Filtri per stato e capacità di ospitare
    - Visualizzazione disponibilità HOST (chi cucina) e GUEST (chi partecipa)
    - Prenotazione diretta tramite modal
    - Indicatori prenotazioni esistenti

    Layout: 1 colonna mobile, 7 colonne desktop (una per ogni giorno)
--}}

<x-filament-panels::page>
    {{-- ============================================
         SEZIONE CALENDARIO SETTIMANALE
         Mostra calendario solo se ci sono dati
         ============================================ --}}
    @if (count($weekData) > 0)
        <x-filament::section>
            {{-- ============================================
                 HEADER: Toggle vista, Navigazione e filtri
                 ============================================ --}}
            <x-slot name="heading">
                <div class="flex flex-col gap-4 w-full">
                    {{-- Toggle Vista --}}
                    <div class="flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                        <button wire:click="changeViewType('month')" type="button"
                            class="flex items-center gap-2 px-4 py-2 rounded-md transition-all font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Calendario Mensile</span>
                        </button>

                        <button wire:click="changeViewType('week')" type="button"
                            class="flex items-center gap-2 px-4 py-2 rounded-md transition-all font-semibold bg-white dark:bg-gray-700 text-lime-600 dark:text-lime-400 shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Vista Settimanale</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between w-full">
                        {{-- Navigazione Settimanale --}}
                        <div class="flex items-center gap-3">
                            <button wire:click="previousWeek"
                                class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                type="button">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <div class="text-xl font-bold text-slate-600 dark:text-lime-400 px-3">
                                {{ $this->getWeekRange() }}
                            </div>

                            <button wire:click="nextWeek"
                                class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                type="button">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <button wire:click="goToCurrentWeek"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm font-medium text-gray-700 dark:text-gray-300"
                                type="button"
                                title="Vai alla settimana corrente">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Oggi</span>
                            </button>
                        </div>

                        {{-- Filtri --}}
                        <div class="flex items-center gap-3">
                            {{-- Filtro Status --}}
                            <x-filament::input.wrapper class="w-auto">
                                <x-filament::input.select wire:model.live="filterStatus">
                                    <option value="">Tutti gli status</option>
                                    @foreach ($this->getStatusFilterOptions() as $groupLabel => $statuses)
                                        <optgroup label="{{ $groupLabel }}">
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </x-filament::input.select>
                            </x-filament::input.wrapper>

                            {{-- Filtro Can Host --}}
                            <label class="flex items-center gap-2 cursor-pointer">
                                <x-filament::input.checkbox wire:model.live="filterCanHost" />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Solo chi può
                                    ospitare</span>
                            </label>
                        </div>
                    </div>
                </div>
            </x-slot>

            {{-- ============================================
                 VISTA SETTIMANALE
                 Layout: 1 colonna mobile, 7 colonne desktop
                 ============================================ --}}
            <div class="grid grid-cols-1 xl:grid-cols-7 gap-0">
                @foreach ($weekData as $dayInfo)
                    <div @class([
                        'border dark:border-gray-500 dark:bg-white/10 border-lime-500 bg-lime-500/20' => $dayInfo[
                            'date'
                        ]->isSameDay(\Carbon\Carbon::now()),
                        'border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-900/50 p-2',
                    ])>
                        {{-- Header giorno --}}
                        <div class="mb-3 border-b border-gray-300 dark:border-gray-600">

                            <div class="flex items-center space-x-2">

                                <div class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                    {{ $dayInfo['day'] }}
                                </div>
                                <div class="text-xs font-bold text-lime-600 dark:text-lime-400 capitalize">
                                    {{ $dayInfo['day_name'] }}
                                </div>
                            </div>
                        </div>

                        {{-- Contenuto giorno --}}
                        <div class="space-y-2">
                            @if ($dayInfo['total_availabilities'] > 0)
                                @foreach ($dayInfo['availabilities'] as $availability)
                                    @php
                                        $statusValue = $availability['status']->value;
                                        $canHost = $availability['can_host'];
                                        if ($canHost) {
                                            $bgClass = 'bg-lime-300';
                                            $textClass = 'text-lime-800';
                                        } else {
                                            $bgClass = 'bg-orange-400';
                                            $textClass = 'text-orange-950';
                                        }

                                        $extraClass = null;
                                        if ($dayInfo['date'] < Carbon\Carbon::today()) {
                                            $extraClass = 'opacity-50';
                                        }
                                    @endphp

                                    <div
                                        class="{{ $bgClass }} {{ $textClass }} {{ $extraClass ?? '' }} rounded-lg p-1">
                                        {{-- Header card --}}
                                        <div class='flex justify-start items-center gap-1'>
                                            @if ($canHost)
                                                @svg('tabler-chef-hat-filled', 'w-4 h-4')
                                            @else
                                                @svg('tabler-tools-kitchen-3', 'w-4 h-4')
                                            @endif
                                            <div class="font-semibold text-sm">
                                                {{ $availability['user_name'] }}
                                            </div>
                                        </div>

                                        {{-- Info posti --}}
                                        @if ($canHost && isset($availability['max_guests']))
                                            <div class="text-sm flex items-center gap-1">
                                                @svg('tabler-armchair', 'w-4 h-4')
                                                <span>
                                                    Posti:
                                                    {{ $availability['total_booked'] ?? 0 }}/{{ $availability['max_guests'] }}
                                                    @if (($availability['available_spots'] ?? 0) > 0)
                                                        <span
                                                            class="font-semibold">({{ $availability['available_spots'] }}
                                                            liberi)</span>
                                                    @else
                                                        <span class="font-semibold text-red-700">(PIENO)</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-sm font-semibold px-5">
                                                {{ $availability['status']->getLabel() }}
                                            </div>

                                            {{-- Indicatore prenotazione --}}
                                            @if (isset($dayInfo['user_booking']) && $dayInfo['user_booking'] && $availability['status']->canUpdateBookings())
                                                @php
                                                    $bookingStatus = $dayInfo['user_booking']['status'];
                                                    $hostName = $dayInfo['user_booking']['host_name'];
                                                    $bookingId = $dayInfo['user_booking']['id'];
                                                @endphp

                                                <a href="/dinner/dinner-bookings/{{ $bookingId }}/edit"
                                                    title="{{ $bookingStatus->getLabel() }}"
                                                    @class([
                                                        'mb-2 px-2 py-1.5 rounded text-xs font-semibold border-1 flex items-center gap-1 hover:shadow-md transition-all',
                                                        'bg-orange-100 border-orange-400 text-orange-800 dark:bg-orange-200 dark:border-orange-600 dark:text-orange-600 hover:bg-orange-200 dark:hover:bg-orange-300' =>
                                                            $bookingStatus->value === 'pending',
                                                        'bg-green-100 border-green-400 text-green-800 dark:bg-green-200 dark:border-green-600 dark:text-green-600 hover:bg-green-200 dark:hover:bg-green-300' =>
                                                            $bookingStatus->value === 'confirmed',
                                                        'bg-red-100 border-red-400 text-red-800 dark:bg-red-200 dark:border-red-600 dark:text-red-600 hover:bg-red-200 dark:hover:bg-red-300' =>
                                                            $bookingStatus->value === 'cancelled',
                                                    ])>
                                                    @svg($bookingStatus->getIcon(), 'w-4 h-4 shrink-0')
                                                    <div class="flex-1">
                                                        <div class="font-bold">{{ $bookingStatus->getLabel() }}
                                                        </div>
                                                    </div>
                                                    @svg('tabler-chevron-right', 'w-4 h-4 flex-shrink-0')
                                                </a>
                                            @endif
                                        @endif

                                        {{-- Pulsante Prenota --}}
                                        @if ($availability['can_book'])
                                            <x-filament::button size="sm" color="primary" class="w-full"
                                                wire:click="openBookingModal({{ $availability['id'] }})">
                                                @svg('tabler-bowl-chopsticks', 'w-4 h-4 mr-1')
                                                Prenota
                                            </x-filament::button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="flex flex-col items-center justify-center text-sm text-gray-400 dark:text-slate-700 text-center py-4">
                                    @svg('tabler-chef-hat-off', 'w-8 h-8 mb-2')
                                    <span>Nessuna disponibilità</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @else
        {{-- ============================================
             STATO VUOTO
             Mostrato quando non ci sono dati per la settimana
             ============================================ --}}
        <x-filament::section>
            <div class="text-center py-12">
                <div class="flex justify-center mb-4">
                    @svg('tabler-calendar-off', 'w-16 h-16 text-gray-400 dark:text-gray-600')
                </div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Nessuna disponibilità per questa settimana
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Non ci sono disponibilità dichiarate dai membri del gruppo per questa settimana.
                </p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
