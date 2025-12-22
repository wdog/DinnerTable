<div class="space-y-3">
    {{-- Informazioni ospite --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
        <div class="flex items-center gap-2 mb-3">
            <x-filament::icon icon="tabler-user-circle" class="h-5 w-5 text-primary-500" />
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Informazioni Ospite</span>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nome completo</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $booking->guest->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Città</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $booking->guest->profile->city ?? 'N/D' }}
                    <br>
                    {{ $booking->guest->profile->address ?? 'N/D' }}
                    {{ $booking->guest->profile->house_number ?? 'N/D' }}
                    {{ $booking->guest->profile->cap ?? 'N/D' }}

                </p>
            </div>
            <div class="">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="tabler-users-group" class="h-5 w-5 text-success-500" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Numero ospiti:</p>
                </div>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $booking->guests_count }}</p>
            </div>


            {{-- Stato prenotazione --}}
            <div class="">
                <div class="flex items-center gap-2 mb-3">
                    <x-filament::icon icon="tabler-calendar-check" class="h-5 w-5 text-info-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Stato</span>
                </div>
                <div class="flex items-center justify-between">
                    <x-filament::badge :color="$booking->status->getColor()" size="lg">
                        {{ $booking->status->getLabel() }}
                    </x-filament::badge>

                </div>
            </div>


            <div>
                <div class="flex items-center gap-2 mb-3">
                    <x-filament::icon icon="tabler-calendar-check" class="h-5 w-5 text-info-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Prenotazione Creata il</span>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $booking->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 mb-3">
                    <x-filament::icon icon="tabler-calendar-check" class="h-5 w-5 text-info-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ultimo Aggiornamento il</span>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $booking->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>



    {{-- Cosa porta --}}
    @if ($booking->bringing_items && count($booking->bringing_items) > 0)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex items-center gap-2 mb-3">
                <x-filament::icon icon="tabler-shopping-bag" class="h-5 w-5 text-success-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cosa porta</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($booking->bringing_items as $item)
                    <x-filament::badge color="success">
                        {{ $item }}
                    </x-filament::badge>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Note --}}
    @if ($booking->notes)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex items-center gap-2 mb-2">
                <x-filament::icon icon="tabler-note" class="h-5 w-5 text-warning-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Note</span>
            </div>
            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->notes }}</p>
        </div>
    @endif
</div>
