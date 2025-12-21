<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Introduzione --}}
        <x-filament::section>
            <x-slot name="heading">
                👋 Benvenuto in DinnerTable!
            </x-slot>

            <div class="text-lg text-gray-700 dark:text-gray-300">
                <p>Organizza cene di gruppo facilmente: crea o unisciti a un gruppo e coordina chi cucina e chi partecipa.</p>
            </div>
        </x-filament::section>

        {{-- Primi Passi --}}
        <x-filament::section>
            <x-slot name="heading">
                🚀 Primi Passi
            </x-slot>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-primary-50 dark:bg-primary-900/20 p-6 rounded-xl border-2 border-primary-200 dark:border-primary-700">
                    <h3 class="text-xl font-bold text-primary-900 dark:text-primary-100 mb-4">
                        📝 Creare un Gruppo
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-primary-600">1.</span>
                            <span>Vai su <strong>Gestione Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-primary-600">2.</span>
                            <span>Clicca <strong>Crea Nuovo Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-primary-600">3.</span>
                            <span>Scegli un nome</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-primary-600">4.</span>
                            <span>Condividi il <strong>codice gruppo</strong></span>
                        </li>
                    </ol>
                </div>

                <div class="bg-success-50 dark:bg-success-900/20 p-6 rounded-xl border-2 border-success-200 dark:border-success-700">
                    <h3 class="text-xl font-bold text-success-900 dark:text-success-100 mb-4">
                        🔗 Unirsi a un Gruppo
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-success-600">1.</span>
                            <span>Vai su <strong>Gestione Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-success-600">2.</span>
                            <span>Clicca <strong>Unisciti a un Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-success-600">3.</span>
                            <span>Inserisci il <strong>codice</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-success-600">4.</span>
                            <span>Sei dentro! 🎉</span>
                        </li>
                    </ol>
                </div>
            </div>
        </x-filament::section>

        {{-- Guida per Host --}}
        <x-filament::section>
            <x-slot name="heading">
                👨‍🍳 Se Cucini Tu
            </x-slot>

            <div class="space-y-6">
                <div class="bg-lime-50 dark:bg-lime-900/20 p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-lime-900 dark:text-lime-100 mb-4">
                        Come funziona
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300 text-lg">
                        <li class="flex gap-3">
                            <span class="font-bold text-lime-600">1.</span>
                            <span>Vai su <strong>Disponibilità</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-lime-600">2.</span>
                            <span>Scegli <strong>"Io Cucino"</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-lime-600">3.</span>
                            <span>Indica <strong>quanti ospiti</strong> puoi accogliere</span>
                        </li>
                    </ol>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-orange-50 dark:from-green-900/20 dark:to-orange-900/20 p-6 rounded-xl border-2 border-green-200 dark:border-green-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                        ⚡ Stati Automatici
                    </h3>
                    <div class="space-y-2 text-base">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-success-500 text-white">
                                Disponibile
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">→ Nessuna prenotazione</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-warning-500 text-white">
                                Quasi pieno
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">→ Ci sono posti liberi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-danger-500 text-white">
                                Pieno
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">→ Tutto prenotato</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Guida per Guest --}}
        <x-filament::section>
            <x-slot name="heading">
                🍽️ Se Partecipi
            </x-slot>

            <div class="space-y-6">
                <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-4">
                        Segnala la tua preferenza
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300 text-lg">
                        <li class="flex gap-3">
                            <span class="font-bold text-purple-600">1.</span>
                            <span>Vai su <strong>Disponibilità</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-purple-600">2.</span>
                            <span>Scegli <strong>"Io Mangio"</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-purple-600">3.</span>
                            <span>Indica se sei <strong>Disponibile</strong> o <strong>Non disponibile</strong></span>
                        </li>
                    </ol>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-xl border-l-4 border-yellow-500">
                    <h3 class="text-xl font-bold text-yellow-900 dark:text-yellow-100 mb-3">
                        ⚠️ IMPORTANTE
                    </h3>
                    <p class="text-lg text-gray-800 dark:text-gray-200 mb-3">
                        Le etichette sono <strong>solo indicative</strong>!
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        Servono a far capire agli host chi è interessato a partecipare, così possono decidere se cucinare.
                    </p>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-4">
                        Come prenotare
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300 text-lg">
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">1.</span>
                            <span>Vai al <strong>Calendario</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">2.</span>
                            <span>Cerca chi cucina (icona 👨‍🍳)</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">3.</span>
                            <span>Clicca <strong>"Prenota"</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">4.</span>
                            <span>Compila: ospiti, cosa porti, note</span>
                        </li>
                    </ol>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-xl border-2 border-blue-200 dark:border-blue-700">
                    <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-4">
                        📋 Regole per le Prenotazioni
                    </h3>
                    <div class="space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="flex gap-3">
                            <span class="text-blue-600 font-bold">✓</span>
                            <span>Devi far parte dello <strong>stesso gruppo</strong> dell'host</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-blue-600 font-bold">✓</span>
                            <span>Ci devono essere <strong>posti disponibili</strong></span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-blue-600 font-bold">✓</span>
                            <span>L'host deve essere in stato <strong>Disponibile</strong> o <strong>Quasi pieno</strong></span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-red-600 font-bold">✗</span>
                            <span>Non puoi prenotare la <strong>tua stessa cena</strong></span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-red-600 font-bold">✗</span>
                            <span>Non puoi prenotare <strong>più di una volta</strong> la stessa cena</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-red-600 font-bold">✗</span>
                            <span>Non puoi prenotare <strong>più cene nello stesso giorno</strong></span>
                        </div>
                    </div>
                    <p class="mt-4 text-base font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 p-3 rounded">
                        💡 Se il pulsante "Prenota" non appare, una di queste regole non è soddisfatta
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
