<div class="space-y-4">
    {{-- Informazioni ospite --}}
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Informazioni Ospite</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Nome completo</p>
                <p class="font-medium">{{ $booking->guest->nome }} {{ $booking->guest->cognome }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                <p class="font-medium">{{ $booking->guest->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Città</p>
                <p class="font-medium">{{ $booking->guest->profile->citta ?? 'N/D' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Numero ospiti</p>
                <p class="font-medium text-lg">{{ $booking->guests_count }}</p>
            </div>
        </div>
    </div>

    {{-- Stato prenotazione --}}
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Stato</h3>
        <div class="flex items-center gap-2">
            <x-filament::badge :color="$booking->status->getColor()">
                {{ $booking->status->getLabel() }}
            </x-filament::badge>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Prenotato il {{ $booking->created_at->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    {{-- Cosa porta --}}
    @if($booking->bringing_items && count($booking->bringing_items) > 0)
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Cosa porta</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($booking->bringing_items as $item)
                    <x-filament::badge color="gray">
                        {{ $item }}
                    </x-filament::badge>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Note --}}
    @if($booking->notes)
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Note</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->notes }}</p>
        </div>
    @endif
</div>
