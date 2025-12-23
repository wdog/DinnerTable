<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Introduzione --}}
        <div class="bg-linear-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/40 dark:to-purple-950/40 p-8 rounded-2xl border-2 border-indigo-200 dark:border-indigo-800">
            <h2 class="text-2xl font-bold text-indigo-900 dark:text-indigo-100 mb-4 flex items-center gap-3">
                <span class="text-3xl">👋</span>
                Benvenuto in DinnerTable!
            </h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Organizza cene di gruppo facilmente: crea o unisciti a un gruppo e coordina chi cucina e chi partecipa.
            </p>
        </div>

        {{-- Primi Passi --}}
        <div class="bg-linear-to-r from-blue-50 to-cyan-50 dark:from-blue-950/40 dark:to-cyan-950/40 p-8 rounded-2xl border-2 border-blue-200 dark:border-blue-800">
            <h2 class="text-2xl font-bold text-blue-900 dark:text-blue-100 mb-6 flex items-center gap-3">
                <span class="text-3xl">🚀</span>
                Primi Passi
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-blue-300 dark:border-blue-700 shadow-sm">
                    <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-4 flex items-center gap-2">
                        <span class="text-2xl">📝</span>
                        Creare un Gruppo
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600 dark:text-blue-400">1.</span>
                            <span>Vai su <strong>Gestione Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600 dark:text-blue-400">2.</span>
                            <span>Clicca <strong>Crea Nuovo Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600 dark:text-blue-400">3.</span>
                            <span>Scegli un nome</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600 dark:text-blue-400">4.</span>
                            <span>Condividi il <strong>codice gruppo</strong></span>
                        </li>
                    </ol>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-cyan-300 dark:border-cyan-700 shadow-sm">
                    <h3 class="text-xl font-bold text-cyan-900 dark:text-cyan-100 mb-4 flex items-center gap-2">
                        <span class="text-2xl">🔗</span>
                        Unirsi a un Gruppo
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-cyan-600 dark:text-cyan-400">1.</span>
                            <span>Vai su <strong>Gestione Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-cyan-600 dark:text-cyan-400">2.</span>
                            <span>Clicca <strong>Unisciti a un Gruppo</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-cyan-600 dark:text-cyan-400">3.</span>
                            <span>Inserisci il <strong>codice</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-cyan-600 dark:text-cyan-400">4.</span>
                            <span>Sei dentro! 🎉</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Guida per Host --}}
        <div class="bg-linear-to-r from-orange-50 to-amber-50 dark:from-orange-950/40 dark:to-amber-950/40 p-8 rounded-2xl border-2 border-orange-200 dark:border-orange-800">
            <h2 class="text-2xl font-bold text-orange-900 dark:text-orange-100 mb-6 flex items-center gap-3">
                <span class="text-3xl">👨‍🍳</span>
                Se Cucini Tu
            </h2>

            <div class="space-y-6">
                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-orange-300 dark:border-orange-700 shadow-sm">
                    <h3 class="text-xl font-bold text-orange-900 dark:text-orange-100 mb-4">
                        Come funziona
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-orange-600 dark:text-orange-400">1.</span>
                            <span>Vai su <strong>Disponibilità</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-orange-600 dark:text-orange-400">2.</span>
                            <span>Scegli <strong>"Io Cucino"</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-orange-600 dark:text-orange-400">3.</span>
                            <span>Indica <strong>quanti ospiti</strong> puoi accogliere</span>
                        </li>
                    </ol>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-amber-300 dark:border-amber-700 shadow-sm">
                    <h3 class="text-xl font-bold text-amber-900 dark:text-amber-100 mb-4 flex items-center gap-2">
                        <span class="text-2xl">⚡</span>
                        Stati Automatici
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 rounded-lg font-medium bg-green-500 text-white shadow-sm min-w-32 text-center">
                                Disponibile
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">Nessuna prenotazione</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 rounded-lg font-medium bg-yellow-500 text-white shadow-sm min-w-32 text-center">
                                Quasi pieno
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">Ci sono posti liberi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 rounded-lg font-medium bg-red-500 text-white shadow-sm min-w-32 text-center">
                                Pieno
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">Tutto prenotato</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 rounded-lg font-medium bg-red-600 text-white shadow-sm min-w-32 text-center">
                                Annullato
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">Cena cancellata dall'host</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 rounded-lg font-medium bg-blue-500 text-white shadow-sm min-w-32 text-center">
                                Completato
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">Cena terminata (automatico)</span>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-800 dark:text-blue-200 flex items-start gap-2">
                                <span class="text-lg">ℹ️</span>
                                <span>Il giorno dopo la cena, lo stato passa automaticamente a <strong>Completato</strong></span>
                            </p>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-900/30 p-3 rounded-lg border border-orange-200 dark:border-orange-800">
                            <p class="text-sm text-orange-800 dark:text-orange-200 flex items-start gap-2">
                                <span class="text-lg">⚠️</span>
                                <span>Se annulli la cena, tutti gli ospiti riceveranno una <strong>notifica automatica</strong> e le loro prenotazioni verranno cancellate</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guida per Guest --}}
        <div class="bg-linear-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 p-8 rounded-2xl border-2 border-emerald-200 dark:border-emerald-800">
            <h2 class="text-2xl font-bold text-emerald-900 dark:text-emerald-100 mb-6 flex items-center gap-3">
                <span class="text-3xl">🍽️</span>
                Se Partecipi
            </h2>

            <div class="space-y-6">
                <div class="bg-linear-to-r from-yellow-100 to-amber-100 dark:from-yellow-900/50 dark:to-amber-900/50 p-6 rounded-xl border-l-4 border-yellow-500 shadow-sm">
                    <h3 class="text-xl font-bold text-yellow-900 dark:text-yellow-100 mb-3 flex items-center gap-2">
                        <span class="text-2xl">💡</span>
                        Come segnalare disponibilità
                    </h3>
                    <p class="text-lg text-gray-800 dark:text-gray-200 mb-3">
                        Quando vuoi partecipare come ospite:
                    </p>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-yellow-800 dark:text-yellow-300">1.</span>
                            <span>Vai su <strong>Disponibilità</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-yellow-800 dark:text-yellow-300">2.</span>
                            <span>Seleziona il <strong>giorno</strong> in cui sei disponibile</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-yellow-800 dark:text-yellow-300">3.</span>
                            <span>Indica che <strong>non ospiti</strong> (toggle "Ospito io la cena" disattivato)</span>
                        </li>
                    </ol>
                    <p class="mt-4 text-gray-700 dark:text-gray-300 italic">
                        Questo farà vedere agli host che sei interessato a partecipare quel giorno!
                    </p>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-teal-300 dark:border-teal-700 shadow-sm">
                    <h3 class="text-xl font-bold text-teal-900 dark:text-teal-100 mb-4">
                        Come prenotare
                    </h3>
                    <ol class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-3">
                            <span class="font-bold text-teal-600 dark:text-teal-400">1.</span>
                            <span>Vai al <strong>Calendario</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-teal-600 dark:text-teal-400">2.</span>
                            <span>Cerca chi cucina (icona 👨‍🍳)</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-teal-600 dark:text-teal-400">3.</span>
                            <span>Clicca <strong>"Prenota"</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-teal-600 dark:text-teal-400">4.</span>
                            <span>Compila: ospiti, cosa porti, note</span>
                        </li>
                    </ol>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-emerald-300 dark:border-emerald-700 shadow-sm">
                    <h3 class="text-xl font-bold text-emerald-900 dark:text-emerald-100 mb-5 flex items-center gap-2">
                        <span class="text-2xl">📋</span>
                        Regole per le Prenotazioni
                    </h3>
                    <div class="space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="flex gap-3">
                            <span class="text-green-600 dark:text-green-400 font-bold text-xl">✓</span>
                            <span>Devi far parte dello <strong>stesso gruppo</strong> dell'host</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-green-600 dark:text-green-400 font-bold text-xl">✓</span>
                            <span>Ci devono essere <strong>posti disponibili</strong></span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-green-600 dark:text-green-400 font-bold text-xl">✓</span>
                            <span>L'host deve essere in stato <strong>Disponibile</strong> o <strong>Quasi pieno</strong></span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-red-600 dark:text-red-400 font-bold text-xl">✗</span>
                            <span>Non puoi prenotare la <strong>tua stessa cena</strong></span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-red-600 dark:text-red-400 font-bold text-xl">✗</span>
                            <span>Non puoi prenotare <strong>più di una volta</strong> la stessa cena</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-red-600 dark:text-red-400 font-bold text-xl">✗</span>
                            <span>Non puoi prenotare <strong>più cene nello stesso giorno</strong></span>
                        </div>
                    </div>
                    <div class="mt-5 bg-linear-to-r from-blue-100 to-cyan-100 dark:from-blue-900/50 dark:to-cyan-900/50 p-4 rounded-lg border border-blue-300 dark:border-blue-700">
                        <p class="font-medium text-blue-800 dark:text-blue-200 flex items-start gap-2">
                            <span class="text-xl">💡</span>
                            <span>Se il pulsante "Prenota" non appare, una di queste regole non è soddisfatta</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gestione Prenotazioni --}}
        <div class="bg-linear-to-r from-purple-50 to-pink-50 dark:from-purple-950/40 dark:to-pink-950/40 p-8 rounded-2xl border-2 border-purple-200 dark:border-purple-800">
            <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-100 mb-6 flex items-center gap-3">
                <span class="text-3xl">📱</span>
                Gestione e Notifiche
            </h2>

            <div class="space-y-6">
                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-purple-300 dark:border-purple-700 shadow-sm">
                    <h3 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-4 flex items-center gap-2">
                        <span class="text-2xl">📋</span>
                        Visualizza le tue prenotazioni
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        Vai su <strong>Le Mie Prenotazioni</strong> per vedere:
                    </p>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li class="flex gap-2">
                            <span class="text-purple-600 dark:text-purple-400">•</span>
                            <span>Tutte le cene a cui hai prenotato</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-purple-600 dark:text-purple-400">•</span>
                            <span>Modificare numero ospiti e note</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-purple-600 dark:text-purple-400">•</span>
                            <span>Cancellare la tua prenotazione</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-pink-300 dark:border-pink-700 shadow-sm">
                    <h3 class="text-xl font-bold text-pink-900 dark:text-pink-100 mb-4 flex items-center gap-2">
                        <span class="text-2xl">🔔</span>
                        Sistema di Notifiche
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        Riceverai notifiche quando:
                    </p>
                    <div class="space-y-3">
                        <div class="flex gap-3 items-start">
                            <span class="text-xl">❌</span>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">L'host cancella la cena</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">La tua prenotazione viene automaticamente annullata</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 bg-purple-50 dark:bg-purple-900/30 p-3 rounded-lg border border-purple-200 dark:border-purple-800">
                        <p class="text-sm text-purple-800 dark:text-purple-200 flex items-start gap-2">
                            <span class="text-lg">💡</span>
                            <span>Le notifiche appaiono nell'icona 🔔 in alto a destra</span>
                        </p>
                    </div>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/60 p-6 rounded-xl border-2 border-indigo-300 dark:border-indigo-700 shadow-sm">
                    <h3 class="text-xl font-bold text-indigo-900 dark:text-indigo-100 mb-4 flex items-center gap-2">
                        <span class="text-2xl">⏰</span>
                        Limiti Temporali
                    </h3>
                    <div class="space-y-3">
                        <div class="flex gap-3 items-start">
                            <span class="text-xl">🚫</span>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">Prenotazioni e disponibilità passate</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Non puoi più modificare cene del passato</p>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start">
                            <span class="text-xl">✅</span>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">Completamento automatico</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Il giorno dopo la cena, lo stato passa a <strong>Completato</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
