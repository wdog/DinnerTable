<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DinnerTable - Organizza le tue cene di gruppo</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-lime-50 to-green-50 min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-lime-600">DinnerTable</h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/dinner" class="text-gray-700 hover:text-lime-600 transition">Accedi</a>
                    <a href="/dinner/register" class="bg-lime-600 text-white px-4 py-2 rounded-lg hover:bg-lime-700 transition">Registrati</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h2 class="text-5xl font-bold text-gray-900 mb-6">
                    Organizza le tue cene di gruppo
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    DinnerTable è l'applicazione che ti permette di coordinare le cene settimanali con il tuo team.
                    Crea o unisciti a un gruppo, proponi di ospitare e partecipa alle cene!
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="/dinner/register" class="bg-lime-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-lime-700 transition">
                        Inizia ora
                    </a>
                    <a href="#features" class="bg-white text-lime-600 px-8 py-3 rounded-lg text-lg font-semibold border-2 border-lime-600 hover:bg-lime-50 transition">
                        Scopri di più
                    </a>
                </div>
            </div>
        </section>

        <section id="features" class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-center text-gray-900 mb-12">Come funziona</h3>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-lime-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">Crea o unisciti a un team</h4>
                        <p class="text-gray-600">Registrati, crea un nuovo team o unisciti a uno esistente usando un codice team.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-lime-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">Pianifica la settimana</h4>
                        <p class="text-gray-600">Ogni settimana viene creato un nuovo calendario dove puoi proporti per ospitare una cena.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-lime-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">Ospita e partecipa</h4>
                        <p class="text-gray-600">Scegli quando ospitare, specifica il numero massimo di ospiti e coordina le cene del team.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">Pronto a iniziare?</h3>
                <p class="text-xl text-gray-600 mb-8">Registrati ora e inizia a organizzare le tue cene di gruppo!</p>
                <a href="/dinner/register" class="bg-lime-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-lime-700 transition inline-block">
                    Registrati gratuitamente
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-lime-400 mb-4">DinnerTable</h3>
                    <p class="text-gray-400">
                        L'applicazione per organizzare le cene di gruppo con il tuo team.
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Link Utili</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-lime-400 transition">Come funziona</a></li>
                        <li><a href="/dinner/register" class="text-gray-400 hover:text-lime-400 transition">Registrati</a></li>
                        <li><a href="/dinner" class="text-gray-400 hover:text-lime-400 transition">Accedi</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Area Riservata</h4>
                    <ul class="space-y-2">
                        <li><a href="/dinner" class="text-gray-400 hover:text-lime-400 transition">Accedi come Friend</a></li>
                        <li><a href="/admin" class="text-gray-400 hover:text-lime-400 transition">Accedi come Admin</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} DinnerTable. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    @vite('resources/js/app.js')
</body>
</html>
