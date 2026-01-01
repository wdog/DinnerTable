<x-filament-panels::page.simple>
    {{-- Form di registrazione con actions --}}
    {{ $this->content }}

    {{-- Link personalizzati responsive --}}
    <div class="mt-6 space-y-3 text-center">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-4 text-sm">
            <span class="text-gray-600 dark:text-gray-400">Hai già un account?</span>
            <a href="{{ route('filament.dinner.auth.login') }}"
                class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold underline decoration-2 underline-offset-2">
                Accedi
            </a>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition">
                @svg('tabler-arrow-left', 'w-4 h-4')
                <span>Torna alla home</span>
            </a>
        </div>
    </div>

    {{-- CSS responsive per mobile --}}
    <style>
        /* Mobile responsive styles */
        @media (max-width: 640px) {

            .fi-simple-main {
                background-color: white !important;
                padding: 40px;
                margin: 0;
                width: 90vw !important;
            }



            /* Tablet */
            @media (min-width: 641px) {
                .fi-simple-page {}
            }
    </style>
</x-filament-panels::page.simple>
