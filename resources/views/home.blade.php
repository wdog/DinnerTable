<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>DinnerTable - Organizza Cene di Gruppo | Gestione Turni e Prenotazioni Online</title>
    <meta name="description"
        content="DinnerTable semplifica l'organizzazione delle cene di gruppo. Coordina turni, gestisci disponibilità e prenota il tuo posto a tavola. Gratis e facile da usare per team e amici.">
    <meta name="keywords"
        content="organizzare cene gruppo, gestione turni cena, prenotazione cena online, calendario cene, coordinare cene team, app cene gruppo, pianificazione cene settimanali">
    <meta name="author" content="DinnerTable">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="DinnerTable - Organizza Cene di Gruppo Facilmente">
    <meta property="og:description"
        content="Coordina le cene del tuo gruppo senza stress. Gestisci turni, disponibilità e prenotazioni in un'unica piattaforma gratuita.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="DinnerTable">
    <meta property="og:locale" content="it_IT">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DinnerTable - Organizza Cene di Gruppo">
    <meta name="twitter:description"
        content="Semplifica l'organizzazione delle cene di gruppo con DinnerTable. Gratis per sempre!">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-small.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-small.png') }}">

    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "DinnerTable",
      "description": "Piattaforma per organizzare cene di gruppo e gestire turni settimanali",
      "applicationCategory": "LifestyleApplication",
      "operatingSystem": "Web",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR"
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "127"
      }
    }
    </script>

    @vite('resources/css/app.css')
</head>

<body class="bg-lime-50 min-h-screen">
    <nav class="bg-white/90 backdrop-blur-sm shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable Logo" class="h-12 w-auto">
                    {{-- <h1 class="text-2xl font-bold text-lime-700">DinnerTable</h1> --}}
                </div>
                <div class="flex items-center gap-4">
                    <a href="/dinner" class="text-gray-700 hover:text-lime-600 transition font-medium">Accedi</a>
                    <a href="/dinner/register"
                        class="bg-lime-600 text-white px-6 py-2.5 rounded-lg hover:bg-lime-700 transition font-semibold shadow-md">
                        Registrati Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <div class="flex justify-center mb-10">
                    <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable - Organizza Cene di Gruppo"
                        class="h-48 w-auto drop-shadow-lg">
                </div>
                <h2 class="text-6xl font-extrabold text-gray-900 mb-6 leading-tight">
                    Trasforma le Cene di Gruppo<br>
                    <span class="text-lime-600">
                        in Momenti Indimenticabili
                    </span>
                </h2>
                <p class="text-2xl text-gray-700 mb-6 max-w-3xl mx-auto leading-relaxed">
                    DinnerTable è la piattaforma che semplifica l'organizzazione delle cene settimanali con il tuo
                    gruppo.
                </p>
                <p class="text-lg text-gray-600 mb-10 max-w-2xl mx-auto">
                    Dimentica i messaggi infiniti su WhatsApp per decidere chi ospita la cena. Con DinnerTable,
                    coordini turni, gestisci disponibilità e prenoti il tuo posto a tavola in pochi click.
                    Più tempo per cucinare, meno tempo per organizzare!
                </p>
                <div class="flex gap-6 justify-center flex-wrap">
                    <a href="/dinner/register"
                        class="bg-lime-600 text-white px-10 py-4 rounded-xl text-xl font-bold hover:from-lime-700 hover:to-green-700 transition shadow-xl hover:shadow-2xl transform hover:scale-105">
                        Inizia Subito - È Gratis!
                    </a>
                    <a href="#features"
                        class="bg-white text-lime-700 px-10 py-4 rounded-xl text-xl font-bold border-3 border-lime-600 hover:bg-lime-50 transition shadow-lg">
                        Scopri Come Funziona
                    </a>
                </div>
                <p class="text-sm text-gray-500 mt-6">✓ Nessuna carta di credito richiesta · ✓ Setup in 2 minuti · ✓
                    Gratuito per sempre</p>
            </div>
        </section>

        <!-- Benefici Section -->
        <section class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h3 class="text-4xl font-bold text-gray-900 mb-4">Perché Scegliere DinnerTable?</h3>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Più di una semplice agenda: è il tuo assistente personale per cene perfettamente organizzate
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-10">
                    <div class="bg-lime-600 p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="bg-lime-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold mb-3 text-gray-900">Risparmia Tempo Prezioso</h4>
                        <p class="text-gray-700 leading-relaxed">
                            Basta perdere ore a cercare di capire chi può ospitare e quando. Visualizza tutte le
                            disponibilità
                            in un unico calendario intelligente e prenota il tuo posto in un attimo.
                        </p>
                    </div>
                    <div class="bg-lime-600 p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="bg-lime-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold mb-3 text-gray-900">Zero Stress Organizzativo</h4>
                        <p class="text-gray-700 leading-relaxed">
                            DinnerTable gestisce automaticamente le prenotazioni, previene doppie assegnazioni e ti
                            notifica
                            quando è il tuo turno. Tu pensa solo a cosa cucinare di buono!
                        </p>
                    </div>
                    <div class="bg-lime-600 p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="bg-green-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold mb-3 text-gray-900">Rafforza i Legami del Gruppo</h4>
                        <p class="text-gray-700 leading-relaxed">
                            Le cene regolari creano tradizioni e ricordi. Con DinnerTable, mantieni viva la socialità
                            del gruppo senza il peso dell'organizzazione. Più convivialità, meno burocrazia!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Come Funziona Section -->
        <section id="features" class="bg-lime-600 py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h3 class="text-4xl font-bold text-gray-900 mb-4">Come Funziona DinnerTable</h3>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        In soli 3 passaggi semplici, inizia a organizzare le tue cene di gruppo come un professionista
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-12">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div
                            class="absolute -top-4 -left-4 bg-lime-600 text-white w-12 h-12 rounded-full flex items-center justify-center text-2xl font-bold shadow-lg z-10">
                            1
                        </div>
                        <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition h-full">
                            <div
                                class="bg-lime-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-lime-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold mb-4 text-gray-900">Crea il Tuo Gruppo</h4>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                Registrati in pochi secondi e crea un nuovo gruppo per il tuo team, oppure unisciti
                                a un gruppo esistente usando il codice di invito condiviso dai tuoi amici.
                            </p>
                            <p class="text-sm text-gray-600 italic">
                                Ogni gruppo ha un codice univoco che puoi condividere facilmente via email o WhatsApp
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div
                            class="absolute -top-4 -left-4 bg-lime-600 text-white w-12 h-12 rounded-full flex items-center justify-center text-2xl font-bold shadow-lg z-10">
                            2
                        </div>
                        <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition h-full">
                            <div
                                class="bg-lime-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-lime-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold mb-4 text-gray-900">Dichiara la Tua Disponibilità</h4>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                Visualizza il calendario settimanale del gruppo e indica quando puoi ospitare una cena.
                                Specifica la data, l'orario e il numero massimo di ospiti che puoi accogliere.
                            </p>
                            <p class="text-sm text-gray-600 italic">
                                Il sistema previene automaticamente le sovrapposizioni e gestisce le prenotazioni per te
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <div
                            class="absolute -top-4 -left-4 bg-lime-600 text-white w-12 h-12 rounded-full flex items-center justify-center text-2xl font-bold shadow-lg z-10">
                            3
                        </div>
                        <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition h-full">
                            <div
                                class="bg-lime-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-green-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold mb-4 text-gray-900">Prenota e Goditi la Cena</h4>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                Sfoglia le disponibilità pubblicate dagli altri membri e prenota il tuo posto a tavola
                                con un semplice click. Ricevi notifiche e promemoria automatici.
                            </p>
                            <p class="text-sm text-gray-600 italic">
                                Tutto sincronizzato in tempo reale: vedi subito chi ha prenotato e quanti posti
                                rimangono
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-lime-600 py-24">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-5xl font-extrabold text-white mb-6 leading-tight">
                    Smetti di Organizzare,<br>Inizia a Cucinare!
                </h3>
                <p class="text-2xl text-lime-50 mb-4 max-w-3xl mx-auto">
                    Unisciti alle centinaia di gruppi che hanno già scelto DinnerTable
                </p>
                <p class="text-lg text-lime-100 mb-10 max-w-2xl mx-auto">
                    La registrazione è completamente gratuita e non richiede carta di credito.
                    Inizia a organizzare le tue cene in meno di 2 minuti!
                </p>
                <div class="flex gap-6 justify-center flex-wrap">
                    <a href="/dinner/register"
                        class="bg-white text-green-700 px-12 py-5 rounded-xl text-2xl font-bold hover:bg-gray-100 transition shadow-2xl hover:shadow-3xl transform hover:scale-105 inline-flex items-center gap-3">
                        <span>Crea il Tuo Gruppo Gratis</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
                <p class="text-sm text-lime-100 mt-8">
                    ✓ Nessun impegno · ✓ Cancella quando vuoi · ✓ Supporto via email incluso
                </p>
            </div>
        </section>
    </main>

    <footer class="bg-lime-50 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-10 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable" class="h-12 w-auto">
                        <h3 class="text-2xl font-bold text-lime-400">DinnerTable</h3>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        La piattaforma intelligente per organizzare le cene di gruppo.
                        Semplice, veloce e completamente gratuita. Trasforma il caos della
                        pianificazione in momenti di pura convivialità.
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-lime-400">Navigazione</h4>
                    <ul class="space-y-3">
                        <li><a href="#features"
                                class="text-gray-300 hover:text-lime-400 transition flex items-center gap-2">
                                <span>→</span> Come Funziona
                            </a></li>
                        <li><a href="/dinner/register"
                                class="text-lime-300 hover:text-lime-400 transition flex items-center gap-2">
                                <span>→</span> Registrati Gratis
                            </a></li>
                        <li><a href="/dinner"
                                class="text-gray-300 hover:text-lime-400 transition flex items-center gap-2">
                                <span>→</span> Accedi
                            </a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-lime-400">Supporto</h4>
                    <ul class="space-y-3">
                        <li><a href="/dinner"
                                class="text-gray-300 hover:text-lime-400 transition flex items-center gap-2">
                                <span>→</span> Pannello Utente
                            </a></li>
                        <li><a href="/admin"
                                class="text-gray-300 hover:text-lime-400 transition flex items-center gap-2">
                                <span>→</span> Pannello Admin
                            </a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-8 mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">
                        &copy; {{ date('Y') }} DinnerTable. Tutti i diritti riservati.
                        Fatto con ❤️ in Italia
                    </p>
                    <div class="flex gap-6 text-sm text-gray-400">
                        <a href="#" class="hover:text-lime-400 transition">Privacy Policy</a>
                        <a href="#" class="hover:text-lime-400 transition">Termini di Servizio</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @vite('resources/js/app.js')
</body>

</html>
