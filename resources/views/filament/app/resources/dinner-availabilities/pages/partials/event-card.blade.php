<div
    class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)]
pt-4 px-4 pb-2 bg-[#bfef58] dark:bg-[#083751]  rounded  shadow relative">



    <div
        class='absolute top-1/2 -mt-2 hidden lg:block
    group-odd:-right-3
    group-even:-left-3
    h-6 w-6 rotate-45
    bg-[#bfef58] dark:bg-[#083751]'>
    </div>


    {{-- card --}}
    <div class="flex items-start justify-between space-x-2 mb-4">
        <div class="font-bold dark:text-gray-100">


            {{-- CASES --}}
            @if ($log->metadata && isset($log->metadata['event']))
                @switch($log->metadata['event'])
                    @case('created')
                        <div>
                            <p class="text-lg font-semibold text-lime-900 bg-lime-500 px-2 rounded flex items-center gap-2">
                                @svg('tabler-chef-hat-filled')
                                <span>Disponibilità creata</span>
                            </p>
                            <p class="flex text-sm gap-x-2 items-center mt-2">
                                @svg('tabler-users-group', 'inline ')
                                {{ $log->metadata['max_guests'] }} posti disponibili
                            </p>
                        </div>
                    @break

                    @case('status_changed')
                        @php
                            $oldStatusEnum = \App\Enums\DinnerAvailabilityStatus::from($log->metadata['old_status']);
                            $newStatusEnum = \App\Enums\DinnerAvailabilityStatus::from($log->metadata['new_status']);
                            $statusColors = match ($newStatusEnum->value) {
                                'completed' => 'text-lime-900 bg-lime-500',
                                'host_cancelled' => 'text-red-900 bg-red-500',
                                'available_to_host' => 'text-green-900 bg-green-500',
                                default => '',
                            };
                            $statusText = match ($newStatusEnum->value) {
                                'completed' => 'Evento concluso',
                                'host_cancelled' => 'Evento cancellato',
                                'available_to_host' => 'Disponibile ad ospitare',
                                'almost_full' => 'Quasi al completo',
                                'full' => 'Al completo',
                                default => 'Cambio stato',
                            };
                        @endphp
                        <div>
                            <p class="{{ $statusColors }} text-lg font-semibold px-2 rounded-lg flex items-center gap-2">
                                {{ svg($newStatusEnum->getIcon()) }}
                                <span>{{ $statusText }}</span>
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <x-filament::badge :color="$oldStatusEnum->getColor()" :icon="$oldStatusEnum->getIcon()" size="sm" class="p-2">
                                    {{ $oldStatusEnum->getLabel() }}
                                </x-filament::badge>
                                @svg('tabler-arrow-right', '')
                                <x-filament::badge :color="$newStatusEnum->getColor()" :icon="$newStatusEnum->getIcon()" size="sm" class="p-2">
                                    {{ $newStatusEnum->getLabel() }}
                                </x-filament::badge>
                            </div>
                            @if (isset($log->metadata['cancellation_reason']))
                                @php
                                    $cancellationReasonEnum = \App\Enums\CancellationReason::from(
                                        $log->metadata['cancellation_reason'],
                                    );
                                @endphp
                                <p class="text-lg font-bold mt-1 flex items-center gap-1">
                                    @svg('tabler-info-circle', '')
                                    {{ $cancellationReasonEnum->getLabel() }}
                                </p>
                            @endif
                        </div>
                    @break

                    @case('dinner_name_changed')
                        <div>
                            <p class="text-lg font-semibold">
                                Titolo cena modificato
                            </p>
                            <div class="text-sm  mt-1">
                                @if ($log->metadata['old_value'])
                                    <div class="flex items-center gap-1">
                                        @svg('tabler-x', 'text-red-500')
                                        <span class="line-through opacity-50">{{ $log->metadata['old_value'] }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1">
                                    @svg('tabler-check', 'text-green-500')
                                    <span class="font-medium">{{ $log->metadata['new_value'] ?? 'Rimosso' }}</span>
                                </div>
                            </div>
                        </div>
                    @break

                    @case('max_guests_changed')
                        <div>
                            <p class="text-lg font-semibold ">
                                Capacità modificata
                            </p>
                            <p class="text-sm  mt-1">
                                {{ $log->metadata['old_value'] }} @svg('tabler-arrow-right', 'inline') {{ $log->metadata['new_value'] }}
                                posti
                            </p>
                        </div>
                    @break

                    @case('note_changed')
                        <div>
                            <p class="text-lg font-semibold ">
                                Note modificate
                            </p>
                            <p class="text-sm mt-1 italic">
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
                            <p class="text-xl font-semibold ">
                                Prenotazioni cancellate
                            </p>
                            <p class="text-sm  mt-1 flex items-center gap-1">
                                @svg('tabler-users-minus', '')
                                {{ $log->metadata['cancelled_bookings_count'] }}
                                {{ $log->metadata['cancelled_bookings_count'] === 1 ? 'prenotazione cancellata' : 'prenotazioni cancellate' }}
                            </p>
                        </div>
                    @break

                    @case('auto_completed')
                      <div>
                            <p class="text-lg font-semibold ">
                              {{ svg('tabler-lock', 'inline') }}
                                Chiuso
                            </p>
                        </div>
                    @break

                    @default
                        <p class="text-xl ">
                            {{ $log->metadata['event'] }}
                        </p>
                @endswitch
            @else
                <p class="text-xl ">
                    Stato: {{ $log->status }}
                </p>
            @endif

            {{-- END CASES --}}

        </div>
        {{-- date --}}
        <div class="flex flex-col items-end">
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
        class="pt-2 border-t border-gray-400 dark:border-gray-600 flex items-center justify-end gap-2 text-amber-600  dark:text-lime-500">
        @svg('tabler-chef-hat-filled')
        <div>
            {{ $log->user?->name ?? 'Sistema' }}
        </div>
    </div>

</div>
