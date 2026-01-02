{{-- Partial per card evento booking timeline --}}
<div
    class="group relative overflow-hidden rounded-lg w-full lg:w-1/2
    bg-linear-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800
     shadow-sm hover:shadow-md transition-all duration-300">

    @php
        $eventType = $log->metadata['event'] ?? 'default';
        $barColor = match ($eventType) {
            'created' => 'bg-sky-400',
            'status_changed' => $log->metadata['new_status'] === 'cancelled' ? '' : 'bg-sky-400',
            default => 'bg-sky-400',
        };

        $barColorFull = match ($eventType) {
            'created' => 'bg-sky-200',
            'status_changed' => $log->metadata['new_status'] === 'cancelled' ? 'bg-red-400 dark:bg-red-950' : 'bg-sky-50',
            default => 'bg-sky-50',
        };

    @endphp
    {{-- Barra colorata sinistra --}}
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-linear-to-b {{ $barColor }} ">
    </div>

    <div class="pl-5 pr-4 py-3  {{ $barColorFull }}">
        {{-- Descrizione evento --}}
        <div class="flex items-top justify-between">
            @if ($log->metadata && isset($log->metadata['event']))
                @switch($log->metadata['event'])
                    @case('created')
                        <div>
                            <p class="text-sm font-semibold ">
                                Prenotazione creata
                            </p>
                            <p class="text-xs flex items-center gap-1 mt-1">
                                @svg('tabler-users', 'w-3 h-3')
                                {{ $log->metadata['guests_count'] }}
                                {{ $log->metadata['guests_count'] === 1 ? 'ospite' : 'ospiti' }}
                            </p>
                            @if (!empty($log->metadata['bringing_items']) && is_array($log->metadata['bringing_items']))
                                <div class="flex items-center gap-1 mt-2 flex-wrap">
                                    @svg('tabler-bottle', 'w-3 h-3')
                                    @foreach ($log->metadata['bringing_items'] as $item)
                                        <x-filament::badge size="xs" color="info" class="px-2 py-1 ">
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
                            <p class="text-sm font-semibold ">
                                Cambio stato
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-xs">
                                <x-filament::badge size="xs" :color="$oldStatusEnum->getColor()" class="px-2 py-1">
                                    {{ $oldStatusEnum->getLabel() }}
                                </x-filament::badge>
                                @svg('tabler-arrow-right', 'w-3 h-3 text-gray-400')
                                <x-filament::badge size="xs" :color="$newStatusEnum->getColor()" class="px-2 py-1">
                                    {{ $newStatusEnum->getLabel() }}
                                </x-filament::badge>
                            </div>
                        </div>
                    @break

                    @case('guests_count_changed')
                        <div>
                            <p class="text-sm font-semibold ">
                                Numero ospiti modificato
                            </p>
                            <p class="text-xs  mt-1">
                                {{ $log->metadata['old_value'] }} @svg('tabler-arrow-right', 'w-3 h-3 inline') {{ $log->metadata['new_value'] }} ospiti
                            </p>
                        </div>
                    @break

                    @case('bringing_items_changed')
                        <div>
                            <p class="text-sm font-semibold ">
                                Contributo modificato
                            </p>
                            <div class="mt-1">
                                @if (!empty($log->metadata['new_value']))
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @foreach ($log->metadata['new_value'] as $item)
                                            <x-filament::badge size="xs" color="info" class="px-2 py-1 ">
                                                {{ $item }}
                                            </x-filament::badge>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs  italic">Rimosso</p>
                                @endif
                            </div>
                        </div>
                    @break

                    @case('notes_changed')
                        <div>
                            <p class="text-sm font-semibold ">
                                Note modificate
                            </p>
                            <p class="text-xs mt-1 font-semibold">
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
            <div class='flex space-x-4 '>
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
