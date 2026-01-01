<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Sezione Info Evento --}}
        <x-filament::section heading="Informazioni Evento" icon="tabler-calendar">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Data cena --}}
                <div>
                    <dt class="font-semibold text-sm text-gray-600 dark:text-gray-400 mb-1">Data Cena</dt>
                    <dd class="text-lg">{{ $record->dinnerDate->dinner_date->isoFormat('dddd, D MMMM YYYY') }}</dd>
                </div>

                {{-- Ruolo (Host/Guest) --}}
                <div>
                    <dt class="font-semibold text-sm text-gray-600 dark:text-gray-400 mb-1">Ruolo</dt>
                    <dd class="text-lg flex items-center gap-2">
                        @if ($record->can_host)
                            @svg('tabler-chef-hat-filled', 'w-5 h-5 text-success-600')
                            <span>Host</span>
                        @else
                            @svg('tabler-tools-kitchen-3', 'w-5 h-5 text-info-600')
                            <span>Ospite</span>
                        @endif
                    </dd>
                </div>

                {{-- Stato --}}
                <div>
                    <dt class="font-semibold text-sm text-gray-600 dark:text-gray-400 mb-1">Stato</dt>
                    <dd>
                        <x-filament::badge :color="$record->status->getColor()">
                            {{ $record->status->getLabel() }}
                        </x-filament::badge>
                    </dd>
                </div>

                {{-- Max ospiti (solo host) --}}
                @if ($record->can_host && $record->max_guests)
                    <div>
                        <dt class="font-semibold text-sm text-gray-600 dark:text-gray-400 mb-1">Capacità Massima</dt>
                        <dd class="text-lg">{{ $record->max_guests }} ospiti</dd>
                    </div>
                @endif
            </dl>
        </x-filament::section>

        {{-- Sezione Host (solo se can_host = true) --}}
        @if ($record->can_host)
            <x-filament::section heading="Dettagli Hosting" icon="tabler-home">
                {{-- Titolo cena --}}
                @if ($record->dinner_name)
                    <div class="mb-6">
                        <dt class="font-semibold text-sm text-gray-600 dark:text-gray-400 mb-1">Titolo Cena</dt>
                        <dd class="text-xl font-medium">{{ $record->dinner_name }}</dd>
                    </div>
                @endif

                {{-- Statistiche prenotazioni --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <dt class="text-sm text-gray-500 dark:text-gray-400 mb-2">Prenotazioni Totali</dt>
                        <dd class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $record->bookings->count() }}
                        </dd>
                    </div>
                    <div class="text-center p-4 bg-success-50 dark:bg-success-900/20 rounded-lg">
                        <dt class="text-sm text-success-700 dark:text-success-400 mb-2">Confermate</dt>
                        <dd class="text-3xl font-bold text-success-900 dark:text-success-100">
                            {{ $record->bookings->where('status', 'confirmed')->count() }}
                        </dd>
                    </div>
                    <div class="text-center p-4 bg-info-50 dark:bg-info-900/20 rounded-lg">
                        <dt class="text-sm text-info-700 dark:text-info-400 mb-2">Posti Rimanenti</dt>
                        <dd class="text-3xl font-bold text-info-900 dark:text-info-100">
                            {{ $record->available_spots }}
                        </dd>
                    </div>
                </div>

                {{-- Lista prenotazioni --}}
                @if ($record->bookings->isNotEmpty())
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Prenotazioni Ricevute</h4>
                        <div class="space-y-2">
                            @foreach ($record->bookings as $booking)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold">
                                            {{ strtoupper(substr($booking->guest->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $booking->guest->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $booking->guests_count }}
                                                {{ $booking->guests_count === 1 ? 'ospite' : 'ospiti' }}
                                                @if ($booking->bringing_items && count($booking->bringing_items) > 0)
                                                    • Porta: {{ implode(', ', $booking->bringing_items) }}
                                                @endif
                                            </div>
                                            @if ($booking->notes)
                                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                    Note: {{ $booking->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <x-filament::badge :color="$booking->status->getColor()">
                                        {{ $booking->status->getLabel() }}
                                    </x-filament::badge>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        @svg('tabler-calendar-off', 'w-12 h-12 mx-auto mb-3 opacity-50')
                        <p>Nessuna prenotazione ricevuta ancora</p>
                    </div>
                @endif
            </x-filament::section>
        @endif

        {{-- Note (se presenti) --}}
        @if ($record->note)
            <x-filament::section heading="Note" icon="tabler-note">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $record->note }}</p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
