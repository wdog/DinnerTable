<x-filament-panels::page>
    <div class="space-y-6">
        {{-- SEZIONE 1: Header Compatto --}}
        <x-filament::section>


            {{-- Titolo Cena (solo host) --}}
            @if ($record->dinner_name)
                <div class="mb-6 text-center py-2 border-y border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $record->dinner_name }}
                    </h3>
                </div>
            @endif

            {{-- Info Evento Grid --}}
            <div class="grid grid-cols-3 lg:grid-cols-3 gap-4 mb-6">
                {{-- Data --}}
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Data Cena</div>
                    <div class="flex items-center gap-2">
                        @svg('tabler-calendar', 'w-4 h-4 text-gray-500')
                        <span class="font-medium">{{ $record->dinnerDate->dinner_date->isoFormat('D MMM YYYY') }}</span>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Stato</div>
                    <x-filament::badge :color="$record->status->getColor()">
                        {{ $record->status->getLabel() }}
                    </x-filament::badge>
                </div>


                {{-- Capacità (solo host) --}}
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Capacità</div>
                    <div class="flex items-center gap-2">
                        @svg('tabler-users', 'w-4 h-4 text-gray-500')
                        <span class="font-medium">{{ $record->max_guests }} posti</span>
                    </div>
                </div>
            </div>



            {{-- Stats Inline (solo host) --}}

            <div class="">
                <div class="grid grid-col-1 lg:grid-cols-3 w-full gap-y-2">
                    <div class="w-full pr-12">
                        <div
                            class="flex gap-y-2 flex-col lg:flex-row items-center px-5 py-6 shadow-sm rounded-md bg-slate-600/20 ">
                            <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75">
                                @svg('tabler-users', 'w-8 h-8 text-white')
                            </div>

                            <div class="mx-5 text-center">
                                <h4 class="font-semibold text-2xl text-gray-700 dark:text-indigo-600">
                                    {{ $record->bookings->count() }}</h4>
                                <div class="text-gray-500">Prenotazioni</div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full pr-12">
                        <div
                            class="flex gap-y-2 flex-col lg:flex-row items-center px-5 py-6 shadow-sm rounded-md bg-slate-600/20">
                            <div class="p-3 rounded-full bg-lime-600 bg-opacity-75">
                                @svg('tabler-users', 'w-8 h-8 text-white')
                            </div>

                            <div class="mx-5 text-center">
                                <h4 class="font-semibold text-2xl  text-gray-700 dark:text-lime-600 ">
                                    {{ $record->bookings->where('status', 'confirmed')->count() }}</h4>
                                <div class="text-gray-500">Confermate</div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full pr-12">
                        <div
                            class="flex gap-y-2 flex-col lg:flex-row items-center px-5 py-6 shadow-sm rounded-md bg-slate-600/20">
                            <div class="p-3 rounded-full bg-pink-600 bg-opacity-75">
                                @svg('tabler-users', 'w-8 h-8 text-white')
                            </div>

                            <div class="mx-5 text-center">
                                <h4 class="text-2xl font-semibold text-gray-700 dark:text-pink-600">
                                    {{ $record->available_spots }}</h4>
                                <div class="text-gray-500">Rimanenti</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Note Collapsible (se presenti) --}}
            @if ($record->note)
                <details class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <summary
                        class="cursor-pointer text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                        @svg('tabler-note', 'w-8 h-8 inline') Note
                    </summary>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $record->note }}
                    </p>
                </details>
            @endif
        </x-filament::section>

        {{-- SEZIONE 2: Prenotazioni Card Mini (solo host) --}}
        @if ($record->can_host && $record->confirmedBookings->isNotEmpty())
            <x-filament::section heading="Prenotazioni Confermate" icon="tabler-users">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($record->confirmedBookings as $booking)
                        <div class="flex items-center gap-3 p-3 bg-slate-600/20 h-32 w-64  rounded-lg ">
                            {{-- Avatar --}}
                            <div
                                class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold flex-shrink-0">
                                {{ strtoupper(substr($booking->guest->name, 0, 1)) }}
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm truncate text-gray-900 dark:text-gray-100">
                                    {{ $booking->guest->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $booking->guests_count }}
                                    {{ $booking->guests_count === 1 ? 'ospite' : 'ospiti' }}
                                </div>

                                {{-- Bringing items inline --}}

                                @if ($booking->bringing_items && count($booking->bringing_items) > 0)
                                    <div class="text-xs text-gray-400 truncate mt-0.5">
                                        🍷 {{ implode(', ', $booking->bringing_items) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Badge status --}}
                            <x-filament::badge size="sm" :color="$booking->status->getColor()">
                                {{ $booking->status->getLabel() }}
                            </x-filament::badge>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @elseif ($record->can_host)
            {{-- Empty state --}}
            <x-filament::section heading="Prenotazioni Ricevute" icon="tabler-users">
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    @svg('tabler-calendar-off', 'w-12 h-12 mx-auto mb-3 opacity-50')
                    <p>Nessuna prenotazione ricevuta ancora</p>
                </div>
            </x-filament::section>
        @endif

        {{-- SEZIONE 3: Cronologia Eventi --}}
        @if ($record->logs->isNotEmpty())
            <x-filament::section heading="Cronologia Eventi" icon="tabler-timeline">
                <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-3">
                    @foreach ($record->logs as $log)
                        <li class="mb-6 ml-6">
                            {{-- Timeline dot --}}
                            <span
                                class="absolute flex items-center justify-center w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900">
                                @svg('tabler-circle-dot-filled', 'w-3 h-3 text-primary-600 dark:text-primary-400')
                            </span>

                            {{-- Event content --}}
                            <div
                                class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                                {{-- Header: Status Badge + Timestamp --}}
                                <div class="flex items-center justify-between mb-2">
                                    <x-filament::badge :color="match ($log->status) {
                                        'AVAILABLE_TO_HOST' => 'success',
                                        'ALMOST_FULL' => 'warning',
                                        'FULL' => 'danger',
                                        'HOST_CANCELLED' => 'danger',
                                        'COMPLETED' => 'gray',
                                        'AVAILABLE' => 'info',
                                        'NOT_AVAILABLE' => 'gray',
                                        default => 'gray',
                                    }">
                                        {{ $log->status }}
                                    </x-filament::badge>

                                    <time class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->created_at->diffForHumans() }}
                                    </time>
                                </div>

                                {{-- Descrizione evento --}}
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    @if ($log->metadata && isset($log->metadata['event']))
                                        @switch($log->metadata['event'])
                                            @case('created')
                                                Disponibilità creata come <strong>Host</strong>
                                                ({{ $log->metadata['max_guests'] }} posti)
                                            @break

                                            @case('status_changed')
                                                Stato cambiato da
                                                <span class="font-medium">{{ $log->metadata['old_status'] }}</span>
                                                a
                                                <span class="font-medium">{{ $log->metadata['new_status'] }}</span>

                                                @if (isset($log->metadata['cancellation_reason']))
                                                    <br>
                                                    <span class="text-xs">Motivo:
                                                        {{ $log->metadata['cancellation_reason'] }}</span>
                                                @endif
                                            @break

                                            @case('auto_completed')
                                                Evento completato automaticamente dal sistema
                                            @break

                                            @default
                                                Evento: {{ $log->metadata['event'] }}
                                        @endswitch
                                    @else
                                        Stato: {{ $log->status }}
                                    @endif
                                </p>

                                {{-- Utente --}}
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($log->user)
                                        @svg('tabler-user', 'w-3 h-3')
                                        <span>{{ $log->user->name }}</span>
                                    @else
                                        @svg('tabler-robot', 'w-3 h-3')
                                        <span>Sistema automatico</span>
                                    @endif

                                    <span class="mx-1">•</span>
                                    <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
