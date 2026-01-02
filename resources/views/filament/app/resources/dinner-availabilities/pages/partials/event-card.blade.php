{{-- Partial per card evento timeline --}}
<div
    class="
    inline-block
     gap-y-2 flex-col lg:flex-row items-center px-5 py-6
                            relative group overflow-hidden
                            rounded-xl bg-linear-to-br from-emerald-100 to-lime-100
                            dark:from-slate-600 dark:to-slate-900
                            border border-gray-200 dark:border-gray-700 shadow-sm
                            hover:shadow-lg transition-all duration-300 hover:-translate-y-1
    ">

    {{-- Header: Badge + Timestamp --}}
    <div class="flex items-center justify-between gap-3 mb-3">
        @php
            $statusEnum = \App\Enums\DinnerAvailabilityStatus::from($log->status);
        @endphp
        <x-filament::badge :color="$statusEnum->getColor()">
            {{ $statusEnum->getLabel() }}
        </x-filament::badge>

        <time class="text-xs text-gray-500 dark:text-gray-100 whitespace-nowrap">
            {{ $log->created_at->format('d/m/Y H:i') }}
        </time>
    </div>

    {{-- Descrizione evento --}}
    <div class="mb-3">
        @if ($log->metadata && isset($log->metadata['event']))
            @switch($log->metadata['event'])
                @case('created')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-sparkles', 'w-4 h-4 inline-block mr-1')
                        Disponibilità creata
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Creata con {{ $log->metadata['max_guests'] }} posti
                    </p>
                @break

                @case('status_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-arrows-exchange', 'w-4 h-4 inline-block mr-1')
                        Cambio stato
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Da <span class="font-medium text-gray-700 dark:text-gray-300">{{ $log->metadata['old_status'] }}</span>
                        a <span class="font-medium text-gray-700 dark:text-gray-300">{{ $log->metadata['new_status'] }}</span>
                    </p>
                    @if (isset($log->metadata['cancellation_reason']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                            Motivo: {{ $log->metadata['cancellation_reason'] }}
                        </p>
                    @endif
                @break

                @case('dinner_name_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-pencil', 'w-4 h-4 inline-block mr-1')
                        Titolo cena modificato
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        @if ($log->metadata['old_value'])
                            Da: <span class="line-through">{{ $log->metadata['old_value'] }}</span><br>
                        @endif
                        A: <span class="font-medium">{{ $log->metadata['new_value'] ?? 'Rimosso' }}</span>
                    </p>
                @break

                @case('max_guests_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-users', 'w-4 h-4 inline-block mr-1')
                        Posti modificati
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Da {{ $log->metadata['old_value'] }} a {{ $log->metadata['new_value'] }} posti
                    </p>
                @break

                @case('note_changed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-note', 'w-4 h-4 inline-block mr-1')
                        Note modificate
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        @if ($log->metadata['old_value'])
                            <span class="italic">"{{ Str::limit($log->metadata['old_value'], 50) }}"</span>
                        @else
                            <span class="text-gray-500">Aggiunte note</span>
                        @endif
                    </p>
                @break

                @case('auto_completed')
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        @svg('tabler-check', 'w-4 h-4 inline-block mr-1')
                        Completamento automatico
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Evento completato dal sistema
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

    {{-- Footer: Utente --}}
    <div class="flex items-center gap-2 text-xs text-lime-600 dark:text-lime-400">
        @if ($log->user)
            @svg('tabler-chef-hat-filled', 'w-3 h-3')
            <span class="">{{ $log->user->name }}</span>
        @else
            @svg('tabler-robot', 'w-3 h-3')
            <span>Sistema automatico</span>
        @endif
        <span class="mx-1">•</span>
        <span class="text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
    </div>
</div>
