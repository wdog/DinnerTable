<x-filament-panels::page>
    {{-- Calendario --}}
    @if (count($calendarData) > 0)
        <x-filament::section>
            {{-- Header con navigazione mese e filtri --}}
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    {{-- Navigazione mese --}}
                    <div class="flex items-center gap-3">
                        {{-- Freccia precedente --}}
                        <button wire:click="previousMonth"
                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            type="button">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        {{-- Selettore mese/anno --}}
                        <select wire:model.live="selectedMonth"
                            class="text-xl font-bold text-custom-600 dark:text-custom-400 capitalize bg-transparent border-0 focus:ring-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg px-3 py-1 transition-colors">
                            @foreach ($this->getMonthOptions() as $value => $label)
                                <option value="{{ $value }}" class="text-gray-900 dark:text-gray-100">
                                    {{ $label }}</option>
                            @endforeach
                        </select>

                        {{-- Freccia successiva --}}
                        <button wire:click="nextMonth"
                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            type="button">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    {{-- Filtri --}}
                    <div class="flex items-center gap-3">
                        {{-- Filtro Status --}}
                        <x-filament::input.wrapper class="w-auto">
                            <x-filament::input.select wire:model.live="filterStatus">
                                <option value="">Tutti gli status</option>
                                <optgroup label="Host (chi cucina)">
                                    <option value="available_to_host">Disponibile ad ospitare</option>
                                    <option value="almost_full">Quasi pieno</option>
                                    <option value="full">Pieno</option>
                                    <option value="host_cancelled">Cancellato (host)</option>
                                </optgroup>
                                <optgroup label="Guest (chi mangia)">
                                    <option value="available">Disponibile</option>
                                    <option value="booked">Ha prenotato</option>
                                    <option value="unavailable">Non disponibile</option>
                                </optgroup>
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
            </x-slot>
            {{-- Header giorni della settimana --}}
            <div class=" lg:grid-cols-7 grid-cols-3 gap-2 mb-2 hidden lg:grid">
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Lun</div>
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Mar</div>
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Mer</div>
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Gio</div>
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Ven</div>
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Sab</div>
                <div class="text-center font-semibold text-xl text-gray-700 dark:text-lime-400/60">Dom</div>
            </div>

            {{-- Griglia calendario --}}
            <div class="grid grid-cols-3 lg:grid-cols-7 gap-2">
                @foreach ($calendarData as $dateInfo)
                    @if ($dateInfo['empty'])
                        {{-- Cella vuota invisibile --}}
                        <div class="aspect-square"></div>
                    @else
                        {{-- Cella giorno --}}
                        <div
                            class="border border-gray-200 dark:border-gray-700 rounded-lg px-2 hover:shadow-lg transition-shadow duration-200 bg-gray-50 dark:bg-gray-900/50 min-h-30 flex flex-col {{ $dateInfo['is_closed'] ? 'opacity-60' : '' }}">

                            {{-- Header giorno --}}
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-lg font-bold text-custom-600 dark:text-custom-400">
                                    {{ $dateInfo['day'] }}
                                    <span class="text-sm text-gray-600 dark:text-lime-400/60 lg:hidden">
                                        {{ Carbon\Carbon::parse($dateInfo['date'])->isoFormat('dddd') }}
                                    </span>
                                </span>
                                @if ($dateInfo['is_closed'])
                                    <span class="text-xs text-red-600">✗</span>
                                @endif
                            </div>

                            {{-- Contenuto --}}
                            <div class="flex-1 space-y-1">
                                @if ($dateInfo['total_availabilities'] > 0)
                                    <div class="space-y-0.5 mt-2">
                                        @foreach ($dateInfo['availabilities'] as $availability)
                                            @php
                                                $statusValue = $availability['status']->value;
                                                $canHost = $availability['can_host'];
                                                // COLOR
                                                if ($canHost) {
                                                    $bgClass = 'bg-lime-300';
                                                    $textClass = 'text-lime-800';
                                                } else {
                                                    $bgClass = 'bg-pink-400';
                                                    $textClass = 'text-pink-950';
                                                }
                                            @endphp



                                            <div class=" {{ $bgClass }} {{ $textClass }} rounded p-2">

                                                {{-- Badge con colore basato su status --}}
                                                <div class='flex justify-start items-center gap-1 mb-2'>

                                                    @if ($canHost)
                                                        <div>
                                                            @svg('tabler-chef-hat-filled', 'w-4 h-4')
                                                        </div>
                                                    @else
                                                        <div>@svg('tabler-tools-kitchen-3', 'w-4 h-4')</div>
                                                    @endif

                                                    <div class="w-full ">
                                                        <div class="sm:text-xs/tight lg:text-base/tight font-semibold">
                                                            {{ $availability['user_name'] }}
                                                        </div>
                                                        <div class="text-xs/tight ">
                                                            {{-- Icona status --}}
                                                            {{ \App\Enums\DinnerAvailabilityStatus::tryFrom($statusValue)->getLabel() }}
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Info posti disponibili (solo per host) --}}
                                                @if ($canHost && isset($availability['max_guests']))
                                                    <div class="text-xs mb-2 flex items-center gap-1">
                                                        @svg('tabler-users', 'w-3 h-3')
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
                                                @endif
                                                <code>
                                                    ===
                                                    {{ $availability['id'] }}
                                                    ===
                                                    {{ $availability['can_book'] }}
                                                    ===
                                                </code>

                                                {{-- Pulsante prenota --}}
                                                @if ($availability['can_book'])
                                                    <x-filament::button size="xs" color="success" class="w-full"
                                                        wire:click="openBookingModal({{ $availability['id'] }})">
                                                        @svg('tabler-circle-plus', 'w-4 h-4 mr-1')
                                                        Prenota
                                                    </x-filament::button>
                                                @endif
                                            </div>
                                        @endforeach

                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 dark:text-gray-500 text-center py-2">
                                        Nessuna disponibilità
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-600 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Nessun evento trovato
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Non ci sono date programmate per questo mese nel tuo gruppo cena.
                </p>
            </div>
        </x-filament::section>
    @endif
    <x-filament-actions::modals />
</x-filament-panels::page>
