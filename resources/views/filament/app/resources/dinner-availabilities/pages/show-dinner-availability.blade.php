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
                <div class="grid grid-cols-3 w-full gap-y-2">
                    <div class="w-full pr-12 ">
                        <div
                            class="flex gap-y-2 flex-col lg:flex-row items-center px-5 py-4
                            relative group overflow-hidden
                            rounded-xl bg-linear-to-br from-emerald-100 to-lime-100 dark:from-slate-600 dark:to-slate-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
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
                            class="flex gap-y-2 flex-col lg:flex-row items-center px-5 py-4
                            relative group overflow-hidden
                            rounded-xl bg-linear-to-br from-emerald-100 to-lime-100 dark:from-slate-600 dark:to-slate-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
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
                            class="flex gap-y-2 flex-col lg:flex-row items-center px-5 py-4
                            relative group overflow-hidden
                            rounded-xl bg-linear-to-br from-emerald-100 to-lime-100 dark:from-slate-600 dark:to-slate-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
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
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $record->note }}
                </div>
            @endif
        </x-filament::section>

        {{-- SEZIONE 2: Prenotazioni Card Mini (solo host) --}}
        @if ($record->can_host && $record->confirmedBookings->isNotEmpty())
            <x-filament::section heading="Prenotazioni Confermate" icon="tabler-users">
                <div class="grid grid-cols-4 gap-4">
                    @foreach ($record->confirmedBookings as $booking)
                        <div
                            class="relative group overflow-hidden
                            rounded-xl bg-linear-to-br from-emerald-100 to-lime-100
                            dark:from-slate-600 dark:to-slate-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">


                            {{-- Card content --}}
                            <div class="relative p-5">
                                {{-- Header: Avatar + Name + Status Badge --}}
                                <div class="flex items-start gap-x-4 mb-1">
                                    {{-- Avatar con immagine reale o default locale --}}
                                    @php
                                        $hasCustomAvatar =
                                            $booking->guest?->profile?->avatar_url &&
                                            file_exists(public_path('storage/' . $booking->guest->profile->avatar_url));

                                        $avatarUrl = $hasCustomAvatar
                                            ? asset('storage/' . $booking->guest->profile->avatar_url)
                                            : asset('images/default-avatar.svg');
                                    @endphp

                                    <img src="{{ $avatarUrl }}" alt="{{ $booking->guest->name }}"
                                        class="w-14 h-14 rounded-full object-cover ring-2 ring-primary-100 dark:ring-primary-900 shrink-0 shadow-md">

                                    {{-- Nome e badge status --}}
                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="font-semibold text-base text-gray-900 dark:text-gray-100 truncate mb-1">
                                            {{ $booking->guest->name }}
                                        </h4>
                                        <x-filament::badge size="sm" :color="$booking->status->getColor()">
                                            {{ $booking->status->getLabel() }}
                                        </x-filament::badge>
                                    </div>
                                </div>

                                {{-- Dettagli prenotazione --}}
                                <div class="space-y-1">
                                    {{-- Numero ospiti --}}
                                    <div class="flex items-center gap-2 text-sm ">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            @svg('tabler-users', 'w-4 h-4 text-primary-600 dark:text-primary-400')
                                        </div>
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">
                                            {{ $booking->guests_count }}
                                            {{ $booking->guests_count === 1 ? 'ospite' : 'ospiti' }}
                                        </span>
                                    </div>

                                    {{-- Cosa porta (solo se presente) --}}
                                    @if ($booking->bringing_items && count($booking->bringing_items) > 0)
                                        <div class="flex items-center gap-2 text-sm ">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-success-100 dark:bg-success-900/30 flex items-center justify-center shrink-0">
                                                @svg('tabler-bottle', 'w-4 h-4 text-success-600 dark:text-success-400')
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    {{ implode(', ', $booking->bringing_items) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Note (se presenti) --}}
                                    @if ($booking->notes)
                                        <div class="flex items-center gap-2 text-sm ">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-info-100 dark:bg-info-900/30 flex items-center justify-center">
                                                @svg('tabler-message-circle', 'w-4 h-4 text-info-600 dark:text-info-400')
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-gray-600 dark:text-gray-400 text-xs leading-relaxed italic">
                                                    "{{ $booking->notes }}"
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom accent bar --}}
                            <div class="absolute bottom-0 w-full h-1 bg-linear-to-r from-primary-500 to-success-500">
                            </div>
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
                <div class="relative">
                    {{-- Linea centrale verticale --}}
                    <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-gray-200 dark:bg-gray-700">
                    </div>

                    <div class="space-y-8">
                        @foreach ($record->logs as $index => $log)
                            @php
                                $isCreation = isset($log->metadata['event']) && $log->metadata['event'] === 'created';
                            @endphp

                            <div class="relative">
                                {{-- Timeline dot centrale --}}
                                <div
                                    class="absolute left-1/2 transform -translate-x-1/2 flex items-center justify-center">
                                    @if ($isCreation)
                                        {{-- Dot speciale per creazione --}}
                                        <div
                                            class="w-12 h-12 rounded-full bg-linear-to-br from-success-500 to-success-600 flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-gray-900">
                                            @svg('tabler-plus', 'w-6 h-6 text-white')
                                        </div>
                                    @else
                                        {{-- Dot normale per altri eventi --}}
                                        <div
                                            class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center shadow-md ring-4 ring-white dark:ring-gray-900">
                                            @svg('tabler-circle-dot-filled', 'w-4 h-4 text-white')
                                        </div>
                                    @endif
                                </div>

                                {{-- Card evento --}}
                                <div class="grid grid-cols-2 gap-8 items-center">
                                    {{-- Contenuto a sinistra o destra alternato --}}
                                    @if ($index % 2 === 0)
                                        {{-- Evento a sinistra --}}
                                        <div class="text-right pr-8">
                                            @include(
                                                'filament.app.resources.dinner-availabilities.pages.partials.event-card',
                                                [
                                                    'log' => $log,
                                                    'isCreation' => $isCreation,
                                                ]
                                            )
                                        </div>
                                        <div></div>
                                    @else
                                        {{-- Evento a destra --}}
                                        <div></div>
                                        <div class="pl-8">
                                            @include(
                                                'filament.app.resources.dinner-availabilities.pages.partials.event-card',
                                                [
                                                    'log' => $log,
                                                    'isCreation' => $isCreation,
                                                ]
                                            )
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
