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
        @if ($record->confirmedBookings->isNotEmpty())
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
        @endif

        {{-- SEZIONE 3: Cronologia Eventi --}}
        @if ($record->logs->isNotEmpty())
            <x-filament::section heading="Cronologia Eventi" icon="tabler-timeline">
                <div class="relative pl-12 max-w-4xl">
                    {{-- Linea verticale principale --}}
                    <div class="absolute left-4 top-2 bottom-0 w-px bg-gray-300 dark:bg-gray-600"></div>

                    <div class="space-y-8">
                        @foreach ($record->logs as $index => $log)
                            @php
                                $isCreation = isset($log->metadata['event']) && $log->metadata['event'] === 'created';
                                $isBooking = $log->loggable_type === 'App\Models\DinnerBooking';
                            @endphp

                            <div class="relative flex gap-4">
                                {{-- Icon status sulla linea --}}
                                <div class="absolute -left-12  top-2">
                                    @php
                                        $eventType = $log->metadata['event'] ?? 'default';

                                        if ($isBooking) {
                                            $statusEnum = \App\Enums\DinnerBookingStatus::from($log->status);
                                            $bgColor = match($eventType) {
                                                'created' => 'bg-cyan-500',
                                                'status_changed' => $log->metadata['new_status'] === 'cancelled' ? 'bg-orange-500' : 'bg-blue-500',
                                                default => 'bg-blue-500',
                                            };
                                        } else {
                                            $statusEnum = \App\Enums\DinnerAvailabilityStatus::from($log->status);
                                            $bgColor = match($eventType) {
                                                'created' => 'bg-emerald-500',
                                                'status_changed' => in_array($log->metadata['new_status'] ?? '', ['host_cancelled', 'not_available']) ? 'bg-orange-500' : 'bg-primary-500',
                                                'host_cancelled_cascade' => 'bg-danger-500',
                                                default => 'bg-primary-500',
                                            };
                                        }
                                    @endphp

                                    {{-- Icona status --}}
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center {{ $bgColor }} shadow-lg ring-4 ring-white dark:ring-gray-900">
                                        @svg($statusEnum->getIcon(), 'w-4 h-4 text-white')
                                    </div>
                                </div>

                                {{-- Contenuto evento --}}
                                <div class="pb-4 flex-1 min-w-0 ">
                                    {{-- Header: Data/Ora --}}
                                    <div class="flex items-center gap-3 mb-3">
                                        @svg('tabler-clock', 'w-4 h-4 text-gray-400 dark:text-gray-500')
                                        <time
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-wide">
                                            {{ $log->created_at->isoFormat('D MMMM YYYY') }}
                                        </time>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                            {{ $log->created_at->isoFormat('HH:mm') }}
                                        </span>
                                    </div>

                                    {{-- Card evento --}}
                                    <div>
                                        @if ($isBooking)
                                            @include(
                                                'filament.app.resources.dinner-availabilities.pages.partials.booking-event-card',
                                                ['log' => $log]
                                            )
                                        @else
                                            @include(
                                                'filament.app.resources.dinner-availabilities.pages.partials.event-card',
                                                ['log' => $log, 'isCreation' => $isCreation]
                                            )
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
