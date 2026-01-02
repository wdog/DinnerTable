{{-- Partial per card evento booking timeline --}}
<div
    class="group relative overflow-hidden rounded-lg border border-blue-200 dark:border-blue-700 w-1/2
    bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all duration-300">

    @php
        $eventType = $log->metadata['event'] ?? 'default';
        $barColor = match ($eventType) {
            'created' => 'from-cyan-500 to-cyan-600 bg-emerald-50 dark:bg-sky-900',
            'status_changed' => $log->metadata['new_status'] === 'cancelled'
                ? 'from-orange-500 to-orange-600 bg-orange-50 dark:bg-orange-900'
                : 'from-blue-500 to-blue-600 ',
            default => 'from-blue-500 to-blue-600',
        };

    @endphp

    {{-- Barra colorata sinistra --}}
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-linear-to-b {{ $barColor }} ">
    </div>

    <div class="pl-5 pr-4 py-3  {{ $barColor }}">
        {{-- Descrizione evento --}}
        <div class="space-y-2">
            @if ($log->metadata && isset($log->metadata['event']))
                @switch($log->metadata['event'])
                    @case('created')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Prenotazione creata
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-1 mt-1">
                                @svg('tabler-users', 'w-3 h-3')
                                {{ $log->metadata['guests_count'] }}
                                {{ $log->metadata['guests_count'] === 1 ? 'ospite' : 'ospiti' }}
                            </p>
                            @if (!empty($log->metadata['bringing_items']) && is_array($log->metadata['bringing_items']))
                                <div class="flex items-center gap-1 mt-2 flex-wrap">
                                    @svg('tabler-bottle', 'w-3 h-3 text-gray-400')
                                    @foreach ($log->metadata['bringing_items'] as $item)
                                        <x-filament::badge size="xs" color="info">
                                            {{ $item }}
                                        </x-filament::badge>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @break

                    @case('status_changed')
                        @php
                            $oldStatusEnum = \App\Enums\DinnerBookingStatus::from($log->metadata['old_status']);
                            $newStatusEnum = \App\Enums\DinnerBookingStatus::from($log->metadata['new_status']);
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
                        </div>
                    @break

                    @case('guests_count_changed')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Numero ospiti modificato
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $log->metadata['old_value'] }} @svg('tabler-arrow-right', 'w-3 h-3 inline') {{ $log->metadata['new_value'] }} ospiti
                            </p>
                        </div>
                    @break

                    @case('bringing_items_changed')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Contributo modificato
                            </p>
                            <div class="mt-1">
                                @if (!empty($log->metadata['new_value']))
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @foreach ($log->metadata['new_value'] as $item)
                                            <x-filament::badge size="xs" color="info">
                                                {{ $item }}
                                            </x-filament::badge>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">Rimosso</p>
                                @endif
                            </div>
                        </div>
                    @break

                    @case('notes_changed')
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Note modificate
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 italic">
                                @if ($log->metadata['new_value'])
                                    <span class="flex items-center gap-1">
                                        @svg('tabler-quote', 'w-3 h-3')
                                        "{{ Str::limit($log->metadata['new_value'], 40) }}"
                                    </span>
                                @else
                                    Note rimosse
                                @endif
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
