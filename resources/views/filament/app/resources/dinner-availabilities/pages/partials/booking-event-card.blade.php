<div
    class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] pt-4 px-4 pb-2
     dark:bg-[#244e57]  bg-[#bab1a4] border-lg
     border-gray-200 dark:border-gray-700 rounded-lg  shadow relative">

    <div class='absolute top-1/2 -mt-2 -left-2 h-4 w-4 rotate-45 dark:bg-[#244e57]  bg-[#bab1a4]'></div>


    <div class="flex items-start justify-between space-x-2 mb-1">
        {{-- card --}}
        <div class="font-bold dark:text-gray-100">
            @if ($log->metadata && isset($log->metadata['event']))
                @switch($log->metadata['event'])
                    @case('created')
                        <div>
                            <p class="text-lg font-semibold ">
                                Prenotazione creata
                            </p>
                            <p class="text-sm flex items-center gap-1 mt-1">
                                @svg('tabler-users')
                                {{ $log->metadata['guests_count'] }}
                                {{ $log->metadata['guests_count'] === 1 ? 'ospite' : 'ospiti' }}
                            </p>
                            @if (!empty($log->metadata['bringing_items']) && is_array($log->metadata['bringing_items']))
                                <div class="flex items-center gap-1 mt-2 flex-wrap">
                                    @svg('tabler-basket')
                                    @foreach ($log->metadata['bringing_items'] as $item)
                                        <x-filament::badge size="sm" color="danger" class="px-2 py-1 ">
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
                            <p class="text-lg font-semibold ">
                                Cambio stato
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-xs">
                                <x-filament::badge size="xs" :color="$oldStatusEnum->getColor()" :icon="$oldStatusEnum->getIcon()" class="px-2 py-1">
                                    {{ $oldStatusEnum->getLabel() }}
                                </x-filament::badge>
                                @svg('tabler-arrow-right')
                                <x-filament::badge size="sm" :color="$newStatusEnum->getColor()" :icon="$newStatusEnum->getIcon()" class="px-2 py-1">
                                    {{ $newStatusEnum->getLabel() }}
                                </x-filament::badge>
                            </div>
                        </div>
                    @break

                    @case('guests_count_changed')
                        <div>
                            <p class="text-lg font-semibold ">
                                Numero ospiti modificato
                            </p>
                            <p class="text-sm  mt-1">
                                {{ $log->metadata['old_value'] }} @svg('tabler-arrow-right', 'inline') {{ $log->metadata['new_value'] }} ospiti
                            </p>
                        </div>
                    @break

                    @case('bringing_items_changed')
                        <div>
                            <p class="text-lg font-semibold ">
                                Contributo modificato
                            </p>
                            <div class="mt-1">
                                @if (!empty($log->metadata['new_value']))
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @foreach ($log->metadata['new_value'] as $item)
                                            <x-filament::badge size="sm" color="info" class="px-2 py-1 ">
                                                {{ $item }}
                                            </x-filament::badge>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-lg italic">Rimosso</p>
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
                                        @svg('tabler-quote', '')
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
                <p class="text-lg">
                    Stato: {{ $log->status }}
                </p>
            @endif
        </div>

        {{-- date --}}
        <div class="flex flex-col items-end text-slate-600 dark:text-slate-400">
            <time class="text-sm font-semibold  tracking-wide">
                @svg('tabler-clock', 'inline ')
                {{ $log->created_at->isoFormat('D MMMM YYYY') }}
                <span class="text-xs">
                    {{ $log->created_at->isoFormat('HH:mm') }}
                </span>
            </time>
        </div>

    </div>

    {{-- user --}}
    <div
        class="pt-2 border-t border-gray-400 dark:border-gray-600 flex items-center justify-end gap-2 text-amber-800  dark:text-lime-500">
        @svg('tabler-bowl-chopsticks-filled', '')
        <div>
            {{ $log->user?->name ?? 'Sistema' }}
        </div>
    </div>

</div>
