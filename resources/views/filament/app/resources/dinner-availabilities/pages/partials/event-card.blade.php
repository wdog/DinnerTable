{{-- Partial per card evento timeline --}}
<div
    class="group relative overflow-hidden rounded-lg  w-full lg:w-1/2
    bg-linear-to-br from-gray-100 to-gray-300 dark:from-gray-800 dark:to-slate-950
     shadow-sm hover:shadow-md transition-all duration-300">
    @php
        $eventType = $log->metadata['event'] ?? 'default';
        $barColor = match ($eventType) {
            'created' => 'bg-lime-500',
            'host_cancelled_cascade' => 'bg-red-500 dark:bg-red-500',
            'status_changed' => in_array($log->metadata['new_status'] ?? '', ['host_cancelled', 'not_available'])
                ? 'bg-red-500 dark:bg-red-500'
                : 'bg-lime-500 dark:bg-lime-500',
            default => 'bg-lime-500',
        };

        $barColorFull = match ($eventType) {
            'created' => 'from-lime-200 to-lime-300 dark:from-emerald-700 dark:to-emerald-900',
            'host_cancelled_cascade' => 'bg-red-400 dark:bg-red-950',
            'status_changed' => in_array($log->metadata['new_status'] ?? '', ['host_cancelled', 'not_available'])
                ? 'bg-red-400 dark:bg-red-950'
                : '',
            default => '',
        };
        $barColorFull = 'bg-linear-to-r ' . $barColorFull;
    @endphp

    {{-- Barra colorata sinistra --}}
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-linear-to-b {{ $barColor }}">
    </div>

    <div class="pl-5 pr-4 py-3 {{ $barColorFull }}">
        {{-- Descrizione evento --}}
        <div class="space-y-2">


            <div class="flex items-top justify-between">

                @if ($log->metadata && isset($log->metadata['event']))
                    @switch($log->metadata['event'])
                        @case('created')
                            <div>
                                <p class="text-sm font-semibold ">
                                    Disponibilità creata
                                </p>
                                <p class="flex text-sm gap-x-2 items-center">
                                    @svg('tabler-users-group', 'w-3.5 h-3.5 inline ')
                                    {{ $log->metadata['max_guests'] }} posti disponibili
                                </p>
                            </div>
                        @break

                        @case('status_changed')
                            @php
                                $oldStatusEnum = \App\Enums\DinnerAvailabilityStatus::from(
                                    $log->metadata['old_status'],
                                );
                                $newStatusEnum = \App\Enums\DinnerAvailabilityStatus::from(
                                    $log->metadata['new_status'],
                                );
                            @endphp
                            <div>
                                <p class="text-sm ">
                                    Cambio stato
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <x-filament::badge size="sm" :color="$oldStatusEnum->getColor()" class="px-2 py-1">
                                        {{ $oldStatusEnum->getLabel() }}
                                    </x-filament::badge>
                                    @svg('tabler-arrow-right', 'w-3 h-3 text-gray-400')
                                    <x-filament::badge size="sm" :color="$newStatusEnum->getColor()" class=" px-2 py-1">
                                        {{ $newStatusEnum->getLabel() }}
                                    </x-filament::badge>
                                </div>
                                @if (isset($log->metadata['cancellation_reason']))
                                    @php
                                        $cancellationReasonEnum = \App\Enums\CancellationReason::from(
                                            $log->metadata['cancellation_reason'],
                                        );
                                    @endphp
                                    <p class="text-sm font-bold mt-1 flex items-center gap-1">
                                        @svg('tabler-info-circle', 'w-3 h-3')
                                        {{ $cancellationReasonEnum->getLabel() }}
                                    </p>
                                @endif
                            </div>
                        @break

                        @case('dinner_name_changed')
                            <div>
                                <p class="text-sm font-semibold ">
                                    Titolo cena modificato
                                </p>
                                <div class="text-xs  mt-1">
                                    @if ($log->metadata['old_value'])
                                        <div class="flex items-center gap-1">
                                            @svg('tabler-x', 'w-3 h-3 ')
                                            <span class="line-through">{{ $log->metadata['old_value'] }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-1">
                                        @svg('tabler-check', 'w-3 h-3 ')
                                        <span class="font-medium">{{ $log->metadata['new_value'] ?? 'Rimosso' }}</span>
                                    </div>
                                </div>
                            </div>
                        @break

                        @case('max_guests_changed')
                            <div>
                                <p class="text-sm font-semibold ">
                                    Capacità modificata
                                </p>
                                <p class="text-xs  mt-1">
                                    {{ $log->metadata['old_value'] }} @svg('tabler-arrow-right', 'w-3 h-3 inline') {{ $log->metadata['new_value'] }}
                                    posti
                                </p>
                            </div>
                        @break

                        @case('note_changed')
                            <div>
                                <p class="text-sm font-semibold ">
                                    Note modificate
                                </p>
                                <p class="text-xs mt-1 italic">
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
                                <p class="text-sm font-semibold ">
                                    Prenotazioni cancellate
                                </p>
                                <p class="text-xs  mt-1 flex items-center gap-1">
                                    @svg('tabler-users-minus', 'w-3 h-3')
                                    {{ $log->metadata['cancelled_bookings_count'] }}
                                    {{ $log->metadata['cancelled_bookings_count'] === 1 ? 'prenotazione cancellata' : 'prenotazioni cancellate' }}
                                </p>
                            </div>
                        @break

                        @default
                            <p class="text-sm ">
                                {{ $log->metadata['event'] }}
                            </p>
                    @endswitch
                @else
                    <p class="text-sm ">
                        Stato: {{ $log->status }}
                    </p>
                @endif

                {{-- user --}}
                <div class='flex space-x-4'>
                    @if ($log->user)
                        @svg('tabler-chef-hat-filled', 'w-4 h-4 ')
                        <span class="text-xs ">{{ $log->user->name }}</span>
                    @else
                        @svg('tabler-robot', 'w-3.5 h-3.5 ')
                        <span class="text-xs ">Sistema</span>
                    @endif
                </div>
            </div>
        </div>


    </div>
</div>
