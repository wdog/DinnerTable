<x-filament-panels::page>
    {{-- Calendario --}}
    @if (count($calendarData) > 0)
        <x-filament::section>
            {{-- Header con navigazione mese --}}
            <x-slot name="heading">
                <div class="flex items-center gap-3 w-full">
                    {{-- Freccia precedente --}}
                    <button wire:click="previousMonth"
                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            type="button">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Selettore mese/anno --}}
                    <select wire:model.live="selectedMonth"
                            class="text-xl font-bold text-custom-600 dark:text-custom-400 capitalize bg-transparent border-0 focus:ring-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg px-3 py-1 transition-colors">
                        @foreach($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}" class="text-gray-900 dark:text-gray-100">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Freccia successiva --}}
                    <button wire:click="nextMonth"
                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            type="button">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </x-slot>
            {{-- Header giorni della settimana --}}
            <div class="grid grid-cols-7 gap-2 mb-2">
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Lun</div>
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Mar</div>
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Mer</div>
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Gio</div>
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Ven</div>
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Sab</div>
                <div class="text-center font-semibold text-sm text-gray-700 dark:text-gray-300">Dom</div>
            </div>

            {{-- Griglia calendario --}}
            <div class="grid grid-cols-7 gap-2">
                @foreach ($calendarData as $dateInfo)
                    @if ($dateInfo['empty'])
                        {{-- Cella vuota invisibile --}}
                        <div class="aspect-square"></div>
                    @else
                        {{-- Cella giorno --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-2 hover:shadow-md transition-shadow duration-200 bg-white dark:bg-gray-800 min-h-[120px] flex flex-col"
                            :class="{{ $dateInfo['is_closed'] ? 'opacity-60' : '' }}">

                            {{-- Header giorno --}}
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-lg font-bold text-custom-600 dark:text-custom-400">
                                    {{ $dateInfo['day'] }}
                                </span>
                                @if ($dateInfo['is_closed'])
                                    <span class="text-xs text-red-600">✗</span>
                                @endif
                            </div>

                            {{-- Contenuto --}}
                            <div class="flex-1 space-y-1">
                                @if ($dateInfo['total_availabilities'] > 0)
                                    <div class="space-y-0.5 mt-2">
                                        @foreach (array_slice($dateInfo['availabilities'], 0, 3) as $availability)
                                            @php
                                                $statusValue = $availability['status']->value;
                                                $canHost = $availability['can_host'];

                                                // Colori basati su status - priorità a can_host
                                                if ($canHost) {
                                                    $bgClass = 'bg-lime-100 dark:bg-lime-900';
                                                    $textClass = 'text-lime-800 dark:text-lime-200';
                                                } elseif ($statusValue === 'available') {
                                                    $bgClass = 'bg-green-100 dark:bg-green-900';
                                                    $textClass = 'text-green-800 dark:text-green-200';
                                                } elseif ($statusValue === 'maybe') {
                                                    $bgClass = 'bg-yellow-100 dark:bg-yellow-900';
                                                    $textClass = 'text-yellow-800 dark:text-yellow-200';
                                                } elseif ($statusValue === 'unavailable') {
                                                    $bgClass = 'bg-red-100 dark:bg-red-900';
                                                    $textClass = 'text-red-800 dark:text-red-200';
                                                } elseif ($statusValue === 'cancelled') {
                                                    $bgClass = 'bg-gray-100 dark:bg-gray-900';
                                                    $textClass = 'text-gray-800 dark:text-gray-200';
                                                } elseif ($statusValue === 'booked') {
                                                    $bgClass = 'bg-purple-100 dark:bg-purple-900';
                                                    $textClass = 'text-purple-800 dark:text-purple-200';
                                                } else {
                                                    $bgClass = 'bg-gray-100 dark:bg-gray-900';
                                                    $textClass = 'text-gray-800 dark:text-gray-200';
                                                }
                                            @endphp


                                            @php
                                                $statusLabel = $availability['status']->getLabel();
                                                $tooltipText = $availability['user_name'] . ' - ' . $statusLabel;
                                                if ($canHost) {
                                                    $tooltipText .= ' (Può ospitare)';
                                                }
                                                if (!empty($availability['note'])) {
                                                    $tooltipText .= ' - ' . $availability['note'];
                                                }
                                            @endphp


                                            <div title="{{ $tooltipText }}"
                                                class=" {{ $bgClass }} {{ $textClass }} rounded">
                                                {{-- Badge con colore basato su status --}}
                                                <a class='flex justify-start items-center gap-1' href="#">
                                                    @if ($canHost)
                                                        <div>@svg('tabler-chef-hat-filled', 'w-4 h-4 ml-1')</div>
                                                    @else
                                                        <div>@svg('tabler-pacman', 'w-4 h-4 ml-1')</div>
                                                    @endif

                                                    <div>
                                                        <div class="text-base">
                                                            {{ $availability['user_name'] }}
                                                        </div>
                                                        <div class="text-xs">
                                                            {{-- Icona status --}}
                                                            @if ($statusValue === 'available')
                                                                <span class="">Available</span>
                                                            @elseif ($statusValue === 'maybe')
                                                                <span class="ml-0.5"> [ ? ]</span>
                                                            @elseif ($statusValue === 'unavailable')
                                                                <span class="ml-0.5"> Unavailable</span>
                                                            @elseif ($statusValue === 'cancelled')
                                                                <span class="ml-0.5"> Cancelled</span>
                                                            @elseif ($statusValue === 'booked')
                                                                <span class="ml-0.5">Booked</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach

                                        {{-- Mostra "+X altri" se ci sono più utenti --}}
                                        @if (count($dateInfo['availabilities']) > 3)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 italic">
                                                +{{ count($dateInfo['availabilities']) - 3 }} altri
                                            </div>
                                        @endif
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
</x-filament-panels::page>
