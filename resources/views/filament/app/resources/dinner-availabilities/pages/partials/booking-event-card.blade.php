{{-- Partial per card evento booking timeline --}}
<div
    class="
    inline-block
     gap-y-2 flex-col lg:flex-row items-center px-4 py-2
                            relative group overflow-hidden
                            rounded-xl bg-linear-to-br from-blue-100 to-cyan-100
                            dark:from-blue-900 dark:to-cyan-900
                            border border-blue-200 dark:border-blue-700 shadow-sm
                            hover:shadow-lg transition-all duration-300 hover:-translate-y-1
    ">

    {{-- Header: Badge + Timestamp --}}
    <div class="flex items-center justify-between gap-3 mb-3">
        @php
            $statusEnum = \App\Enums\DinnerBookingStatus::from($log->status);
        @endphp
        <x-filament::badge :color="$statusEnum->getColor()">
            {{ $statusEnum->getLabel() }}
        </x-filament::badge>

        <time class="text-xs text-gray-500 dark:text-gray-100 whitespace-nowrap">
            {{ $log->created_at->format('d/m/Y H:i') }}
        </time>
    </div>

    {{-- Descrizione evento --}}
    <div class="mb-2">
        @if ($log->metadata && isset($log->metadata['event']))
            @switch($log->metadata['event'])
                @case('created')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-calendar-plus', 'w-4 h-4 inline-block mr-1')
                        Prenotazione creata
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        {{ $log->metadata['guests_count'] }} {{ $log->metadata['guests_count'] === 1 ? 'ospite' : 'ospiti' }}
                    </p>
                    @if (!empty($log->metadata['bringing_items']) && is_array($log->metadata['bringing_items']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Porta:

                            @foreach ($log->metadata['bringing_items'] as $item)
                                <x-filament::badge size="xs" class='bg-sky-400/40 text-slate-800 px-2 '>
                                    {{ $item }}
                                </x-filament::badge>
                            @endforeach

                        </p>
                    @endif
                @break

                @case('status_changed')
                    @php
                        $oldStatusEnum = \App\Enums\DinnerBookingStatus::from($log->metadata['old_status']);
                        $newStatusEnum = \App\Enums\DinnerBookingStatus::from($log->metadata['new_status']);
                    @endphp
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-refresh', 'w-4 h-4 inline-block mr-1')
                        Cambio stato prenotazione
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Da <x-filament::badge class='px-2' size="xs"
                            :color="$oldStatusEnum->getColor()">{{ $oldStatusEnum->getLabel() }}</x-filament::badge>
                        a <x-filament::badge class='px-2' size="xs"
                            :color="$newStatusEnum->getColor()">{{ $newStatusEnum->getLabel() }}</x-filament::badge>
                    </p>
                @break

                @case('guests_count_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-users', 'w-4 h-4 inline-block mr-1')
                        Numero ospiti modificato
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Da {{ $log->metadata['old_value'] }} a {{ $log->metadata['new_value'] }} ospiti
                    </p>
                @break

                @case('bringing_items_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-bottle', 'w-4 h-4 inline-block mr-1')
                        Contributo modificato
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        @if (!empty($log->metadata['new_value']))
                            Porta:
                            @foreach ($log->metadata['new_value'] as $item)
                                <x-filament::badge size="xs" class='bg-sky-400/40 text-slate-800 px-2 '>
                                    {{ $item }}
                                </x-filament::badge>
                            @endforeach
                        @else
                            Rimosso
                        @endif
                    </p>
                @break

                @case('notes_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-message', 'w-4 h-4 inline-block mr-1')
                        Note modificate
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 italic">
                        @if ($log->metadata['new_value'])
                            "{{ Str::limit($log->metadata['new_value'], 50) }}"
                        @else
                            Note rimosse
                        @endif
                    </p>
                @break

                @default
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Evento: {{ $log->metadata['event'] }}
                    </p>
            @endswitch
        @else
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Stato: {{ $log->status }}
            </p>
        @endif
    </div>

    {{-- Footer: Utente (guest) --}}
    <div class="flex items-center gap-2 text-xs text-cyan-600 dark:text-cyan-400">
        @if ($log->user)
            @svg('tabler-user-check', 'w-3 h-3')
            <span>{{ $log->user->name }}</span>
        @else
            @svg('tabler-robot', 'w-3 h-3')
            <span>Sistema</span>
        @endif
        <span class="mx-1">•</span>
        <span class="text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
    </div>
</div>
