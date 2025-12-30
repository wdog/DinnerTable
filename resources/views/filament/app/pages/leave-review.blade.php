<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Intro --}}
        <div class="text-center space-y-2">
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                @svg('tabler-star-filled', 'w-4 h-4 text-amber-400 inline')
                Aiutaci a migliorare DinnerTable. Lascia una recensione e raccontaci la tua esperienza.
            </p>
        </div>

        {{-- Form --}}
        <form wire:submit="submit" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-center">
                <x-filament::button type="submit" size="lg">
                    @svg('tabler-send', 'w-5 h-5')
                    <span>Invia Recensione</span>
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
