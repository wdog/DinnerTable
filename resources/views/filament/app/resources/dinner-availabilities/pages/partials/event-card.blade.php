{{-- Partial per card evento timeline --}}
<div
    class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 w-1/2
    bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all duration-300">

    @php
        $eventType = $log->metadata['event'] ?? 'default';
        $barColor = match($eventType) {
            'created' => 'from-emerald-500 to-emerald-600 bg-emerald-50 dark:bg-emerald-900',
            'status_changed' => in_array($log->metadata['new_status'] ?? '', ['host_cancelled', 'not_available'])
                ? 'from-orange-500 to-orange-600'
                : 'from-primary-500 to-primary-600',
            'host_cancelled_cascade' => 'from-danger-500 to-danger-600',
            default => 'from-primary-500 to-primary-600',
        };
    @endphp

    {{-- Barra colorata sinistra --}}
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-linear-to-b {{ $barColor }}">
    </div>

    <div class="pl-5 pr-4 py-3 {{ $barColor}}">
        {{-- Descrizione evento --}}
        <div class="space-y-2">
            @if ($log->metadata && isset($log->metadata['event']))
                @switch($log->metadata['event'])
                    @case('created')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Disponibilità creata
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-1 mt-1">
                                @svg('tabler-users', 'w-3 h-3')
                                {{ $log->metadata['max_guests'] }} posti disponibili
                            </p>
                        </div>
                    @break

                    @case('status_changed')
                        @php
                            $oldStatusEnum = \App\Enums\DinnerAvailabilityStatus::from($log->metadata['old_status']);
                            $newStatusEnum = \App\Enums\DinnerAvailabilityStatus::from($log->metadata['new_status']);
                        @endphp
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Cambio stato
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-xs">
                                <x-filament::badge size="xs" :color="$oldStatusEnum->getColor()">
                                    {{ $oldStatusEnum->getLabel() }}
                                </x-filament::badge>
                                @svg('tabler-arrow-right', 'w-3 h-3 text-gray-400')
                                <x-filament::badge size="xs" :color="$newStatusEnum->getColor()">
                                    {{ $newStatusEnum->getLabel() }}
                                </x-filament::badge>
                            </div>
                            @if (isset($log->metadata['cancellation_reason']))
                                @php
                                    $cancellationReasonEnum = \App\Enums\CancellationReason::from(
                                        $log->metadata['cancellation_reason'],
                                    );
                                @endphp
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic flex items-center gap-1">
                                    @svg('tabler-info-circle', 'w-3 h-3')
                                    {{ $cancellationReasonEnum->getLabel() }}
                                </p>
                            @endif
                        </div>
                    @break

                    @case('dinner_name_changed')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Titolo cena modificato
                            </p>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                @if ($log->metadata['old_value'])
                                    <div class="flex items-center gap-1">
                                        @svg('tabler-x', 'w-3 h-3 text-danger-500')
                                        <span class="line-through">{{ $log->metadata['old_value'] }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1">
                                    @svg('tabler-check', 'w-3 h-3 text-success-500')
                                    <span class="font-medium">{{ $log->metadata['new_value'] ?? 'Rimosso' }}</span>
                                </div>
                            </div>
                        </div>
                    @break

                    @case('max_guests_changed')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Capacità modificata
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $log->metadata['old_value'] }} @svg('tabler-arrow-right', 'w-3 h-3 inline') {{ $log->metadata['new_value'] }} posti
                            </p>
                        </div>
                    @break

                    @case('note_changed')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Note modificate
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 italic">
                                @if ($log->metadata['old_value'])
                                    "{{ Str::limit($log->metadata['old_value'], 40) }}"
                                @else
                                    Note aggiunte
                                @endif
                            </p>
                        </div>
                    @break

                    @case('host_cancelled_cascade')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Prenotazioni cancellate
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 flex items-center gap-1">
                                @svg('tabler-users-minus', 'w-3 h-3')
                                {{ $log->metadata['cancelled_bookings_count'] }} {{ $log->metadata['cancelled_bookings_count'] === 1 ? 'prenotazione cancellata' : 'prenotazioni cancellate' }}
                            </p>
                        </div>
                    @break

                    @default
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $log->metadata['event'] }}
                        </p>
                @endswitch
            @else
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Stato: {{ $log->status }}
                </p>
            @endif
        </div>

        {{-- Footer: Utente --}}
        <div class="flex items-center gap-2 mt-3 pt-2 border-t border-gray-100 dark:border-gray-700">
            @if ($log->user)
                @svg('tabler-user-circle', 'w-3.5 h-3.5 text-gray-400')
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $log->user->name }}</span>
            @else
                @svg('tabler-robot', 'w-3.5 h-3.5 text-gray-400')
                <span class="text-xs text-gray-600 dark:text-gray-400">Sistema</span>
            @endif
        </div>
    </div>
</div>
