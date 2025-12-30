<!DOCTYPE html>
<html lang="it" class="scroll-smooth">

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
    @vite('resources/css/app.css')
</head>

<body class="bg-white antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-md shadow-sm z-50 border-b border-lime-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <!-- Logo -->
                <div>
                    <a class="flex items-center gap-2" href="/">
                        <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable" class="h-8 md:h-10 w-auto">
                        <span class="hidden sm:block text-sm md:text-base font-bold text-slate-700">DinnerTable</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-6 xl:gap-8">
                    <a href="#hero"
                        class="text-gray-600 hover:text-lime-600 transition font-medium text-sm xl:text-base">Home</a>
                    <a href="#benefits"
                        class="text-gray-600 hover:text-lime-600 transition font-medium text-sm xl:text-base">Vantaggi</a>
                    <a href="#features"
                        class="text-gray-600 hover:text-lime-600 transition font-medium text-sm xl:text-base">Funzionalità</a>
                    <a href="#testimonials"
                        class="text-gray-600 hover:text-lime-600 transition font-medium text-sm xl:text-base">Testimonianze</a>
                    <a href="#contacts"
                        class="text-gray-600 hover:text-lime-600 transition font-medium text-sm xl:text-base">Contatti</a>
                </div>

                <!-- CTA Buttons -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <a href="/dinner"
                        class="hidden sm:block text-gray-600 hover:text-lime-600 transition font-medium text-sm md:text-base">Accedi</a>
                    <a href="/dinner/register"
                        class="bg-lime-500 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-full hover:bg-lime-600 transition-all duration-300 font-semibold shadow-lg shadow-lime-500/30 hover:shadow-xl hover:shadow-lime-500/40 hover:scale-105 text-sm md:text-base">
                        Inizia Gratis
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="lg:hidden p-2 text-gray-600 hover:text-lime-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden lg:hidden pb-4 pt-2">
                <div class="flex flex-col gap-3">
                    <a href="#hero"
                        class="text-gray-600 hover:text-lime-600 transition font-medium py-2 px-4 rounded-lg hover:bg-lime-50">Home</a>
                    <a href="#benefits"
                        class="text-gray-600 hover:text-lime-600 transition font-medium py-2 px-4 rounded-lg hover:bg-lime-50">Vantaggi</a>
                    <a href="#features"
                        class="text-gray-600 hover:text-lime-600 transition font-medium py-2 px-4 rounded-lg hover:bg-lime-50">Funzionalità</a>
                    <a href="#testimonials"
                        class="text-gray-600 hover:text-lime-600 transition font-medium py-2 px-4 rounded-lg hover:bg-lime-50">Testimonianze</a>
                    <a href="#contacts"
                        class="text-gray-600 hover:text-lime-600 transition font-medium py-2 px-4 rounded-lg hover:bg-lime-50">Contatti</a>
                    <a href="/dinner"
                        class="sm:hidden text-gray-600 hover:text-lime-600 transition font-medium py-2 px-4 rounded-lg hover:bg-lime-50">Accedi</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="snap-y snap-mandatory overflow-y-scroll h-screen" style="scroll-padding-top: 4rem;">
        <!-- Hero Section -->
        <section id="hero"
            class="snap-start snap-always relative min-h-screen h-auto flex flex-col justify-start md:justify-center pt-20 md:pt-32 pb-4 md:pb-16 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50 overflow-hidden">
            <!-- Decorative Elements -->
            <div
                class="absolute top-0 left-0 w-96 h-96 bg-lime-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-0 left-1/2 w-96 h-96 bg-lime-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000">
            </div>

            <div class="relative max-w-7xl mx-auto">
                <div class="text-center">
                    <div
                        class="inline-flex items-center gap-2 bg-lime-100 text-lime-800 px-4 py-2 rounded-full text-sm font-semibold mb-8 shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        100% Gratuito · Nessuna Carta Richiesta
                    </div>

                    <h1
                        class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-4 md:mb-6 leading-tight tracking-tight">
                        Organizza Cene di Gruppo
                        <br>
                        <span class="bg-clip-text text-transparent bg-linear-to-r from-lime-600 to-emerald-600">
                            Senza Stress
                        </span>
                    </h1>

                    <p
                        class="text-base sm:text-xl md:text-2xl text-gray-600 mb-6 md:mb-8 max-w-3xl mx-auto leading-relaxed px-2">
                        La piattaforma intelligente che trasforma il caos dell'organizzazione
                        in momenti di pura convivialità. Coordina turni, prenota posti e goditi la cena.
                    </p>

                    <div
                        class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center items-stretch sm:items-center mb-8 md:mb-12 px-2">
                        <a href="/dinner/register"
                            class="group inline-flex items-center justify-center gap-2 md:gap-3 bg-lime-500 text-white px-6 md:px-8 py-3 md:py-4 rounded-full text-base md:text-lg font-bold hover:bg-lime-600 transition-all duration-300 shadow-2xl shadow-lime-500/30 hover:shadow-lime-500/50 hover:scale-105 w-full sm:w-auto">
                            <span>Inizia Gratis Ora</span>
                            <svg class="w-4 h-4 md:w-5 md:h-5 group-hover:translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#features"
                            class="inline-flex items-center justify-center gap-2 bg-white text-gray-700 px-6 md:px-8 py-3 md:py-4 rounded-full text-base md:text-lg font-semibold border-2 border-gray-200 hover:border-lime-500 hover:text-lime-600 transition-all duration-300 shadow-lg hover:shadow-xl w-full sm:w-auto">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Guarda Come Funziona</span>
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div
                        class="flex flex-wrap justify-center items-center gap-4 md:gap-8 text-xs md:text-sm text-gray-500 px-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-yellow-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Setup in 2 minuti</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-yellow-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">100% Gratis</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-yellow-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Nessun vincolo</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pulsante navigazione -->
            <x-section-nav-button target="trust" direction="down" />
        </section>

        <!-- Trust Section -->
        <section id="trust"
            class="snap-start snap-always relative min-h-screen h-auto flex flex-col justify-start
            md:justify-center pt-20 md:pt-2 pb-4 md:pb-2 px-4 sm:px-6
            lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50 overflow-hidden">
            <!-- Decorative background elements -->
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-lime-200 rounded-full
                mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>

            <div class="max-w-7xl mx-auto w-full relative z-10">
                <div class="grid lg:grid-cols-2 gap-8 md:gap-16 items-center">
                    <!-- Immagine a sinistra (nascosta su mobile) -->
                    <div class="relative group hidden lg:block">
                        <!-- Glow effect dietro l'immagine -->
                        <div
                            class="absolute -inset-4 bg-linear-to-r from-lime-400 to-emerald-400 rounded-3xl blur-2xl opacity-20 group-hover:opacity-30 transition-opacity duration-500">
                        </div>

                        <!-- Gradient overlay -->
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-lime-500/10 via-transparent to-emerald-500/10 rounded-2xl z-10">
                        </div>

                        <img src="{{ asset('images/banner_dinner.jpg') }}" alt="DinnerTable Interface"
                            class="relative w-full rounded-2xl shadow-2xl transform group-hover:scale-[1.02] transition-transform duration-500">
                    </div>

                    <!-- Trust Cards a destra -->
                    <div class="relative flex flex-col gap-4 md:gap-6 lg:col-start-2">
                        <!-- Card 1 - 2 minuti -->
                        <div
                            class="flex gap-3 md:gap-4 p-4 md:p-5 bg-white rounded-xl
                            shadow-md hover:shadow-lg transition-shadow duration-300 border border-lime-100">
                            <div class="shrink-0">
                                <div class="bg-lime-100 p-2 md:p-2.5 rounded-xl">
                                    <svg class="w-6 h-6 md:w-8 md:h-8 text-lime-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1 md:mb-2">Setup in 2 minuti
                                </h3>
                                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                                    Registrati, crea il tuo gruppo e inizia subito. Non serve configurazione complicata:
                                    inserisci i dati base, invita i membri e sei pronto per organizzare la prima cena.
                                </p>
                            </div>
                        </div>

                        <!-- Card 2 - 100% Gratis -->
                        <div
                            class="flex gap-3 md:gap-4 p-4 md:p-5 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-emerald-100">
                            <div class="shrink-0">
                                <div class="bg-emerald-100 p-2 md:p-2.5 rounded-xl">
                                    <svg class="w-6 h-6 md:w-8 md:h-8 text-emerald-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1 md:mb-2">100% Gratis per
                                    sempre</h3>
                                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                                    Zero costi nascosti, zero abbonamenti, zero limiti di utilizzo. Tutte le
                                    funzionalità
                                    sono completamente gratuite per sempre. Organizza cene illimitate senza spendere un
                                    centesimo.
                                </p>
                            </div>
                        </div>

                        <!-- Card 3 - Nessun vincolo -->
                        <div
                            class="flex gap-3 md:gap-4 p-4 md:p-5 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-lime-100">
                            <div class="shrink-0">
                                <div class="bg-lime-100 p-2 md:p-2.5 rounded-xl">
                                    <svg class="w-6 h-6 md:w-8 md:h-8 text-lime-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1 md:mb-2">Nessun vincolo o
                                    impegno</h3>
                                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                                    Usa DinnerTable quando vuoi, come vuoi. Nessuna registrazione obbligatoria per i
                                    membri,
                                    nessun contratto vincolante. Puoi modificare o cancellare il gruppo in qualsiasi
                                    momento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pulsante navigazione -->
            <x-section-nav-button target="benefits" direction="down" />
        </section>

        <!-- Benefits Section -->
        <section id="benefits"
            class="snap-start snap-always relative min-h-screen h-auto flex flex-col justify-start md:justify-center pt-20 md:pt-2 pb-4 md:pb-16 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto w-full">
                <div class="text-center mb-3 md:mb-16">
                    <span
                        class="inline-block text-lime-600 font-semibold text-xs md:text-sm uppercase tracking-wider mb-1 md:mb-3">Vantaggi</span>
                    <h2 class="text-xl sm:text-3xl md:text-5xl font-bold text-gray-900 mb-2 md:mb-4 px-2">
                        Perché DinnerTable?
                    </h2>
                </div>

                <div class="flex flex-col sm:grid sm:grid-cols-3 gap-3 md:gap-8 w-full">
                    <!-- Benefit 1 -->
                    <div
                        class="group relative bg-linear-to-br from-lime-50 to-emerald-50 p-3 md:p-8 rounded-xl md:rounded-3xl shadow-md hover:shadow-xl transition-all duration-500 border border-lime-100 w-full">
                        <div class="flex items-start gap-3 md:gap-4 md:block">
                            <div
                                class="bg-lime-100 w-14 h-14 md:w-16 md:h-16 rounded-xl md:rounded-2xl flex items-center justify-center shrink-0 md:mb-6">
                                <svg class="w-7 h-7 md:w-8 md:h-8 text-lime-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base md:text-2xl font-bold mb-2 md:mb-3 text-gray-900">Risparmia Tempo
                                    Prezioso</h3>
                                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                                    Basta chat infinite per organizzare. Visualizza tutte le disponibilità in un
                                    calendario intelligente e prenota con un click.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit 2 -->
                    <div
                        class="group relative bg-linear-to-br from-lime-50 to-emerald-50 p-3 md:p-8 rounded-xl md:rounded-3xl shadow-md hover:shadow-xl transition-all duration-500 border border-emerald-100 w-full">
                        <div class="flex items-start gap-3 md:gap-4 md:block">
                            <div
                                class="bg-emerald-100 w-14 h-14 md:w-16 md:h-16 rounded-xl md:rounded-2xl flex items-center justify-center shrink-0 md:mb-6">
                                <svg class="w-7 h-7 md:w-8 md:h-8 text-emerald-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base md:text-2xl font-bold mb-2 md:mb-3 text-gray-900">Zero Stress
                                    Organizzativo</h3>
                                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                                    Il sistema gestisce automaticamente prenotazioni e notifiche. Tu pensa solo al menu
                                    e alla convivialità.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit 3 -->
                    <div
                        class="group relative bg-linear-to-br from-lime-50 to-emerald-50 p-3 md:p-8 rounded-xl md:rounded-3xl shadow-md hover:shadow-xl transition-all duration-500 border border-lime-100 w-full">
                        <div class="flex items-start gap-3 md:gap-4 md:block">
                            <div
                                class="bg-lime-100 w-14 h-14 md:w-16 md:h-16 rounded-xl md:rounded-2xl flex items-center justify-center shrink-0 md:mb-6">
                                <svg class="w-7 h-7 md:w-8 md:h-8 text-lime-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base md:text-2xl font-bold mb-2 md:mb-3 text-gray-900">Rafforza i
                                    Legami</h3>
                                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                                    Le cene regolari creano tradizioni e ricordi indimenticabili. Mantieni viva la
                                    socialità senza il peso della burocrazia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pulsante navigazione -->
            <x-section-nav-button target="features" direction="down" />
        </section>

        <!-- Features Section -->
        <section id="features"
            class="snap-start snap-always relative min-h-screen h-auto flex flex-col justify-start md:justify-center pt-20 md:pt-2 pb-4 md:pb-16 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50">
            <div class="max-w-7xl mx-auto w-full">
                <div class="text-center mb-3 md:mb-16">
                    <span
                        class="inline-block text-lime-600 font-semibold text-xs md:text-sm uppercase tracking-wider mb-1 md:mb-3">Come
                        Funziona</span>
                    <h2 class="text-xl sm:text-3xl md:text-5xl font-bold text-gray-900 mb-2 md:mb-4 px-2">
                        Semplice Come 1, 2, 3
                    </h2>
                </div>

                <div class="flex flex-col md:grid md:grid-cols-3 gap-3 md:gap-8 w-full">
                    <!-- Step 1 -->
                    <div
                        class="bg-white p-3 md:p-10 rounded-xl md:rounded-3xl shadow-md hover:shadow-xl transition-all duration-500 border border-lime-200 w-full md:relative">
                        <!-- Numero grande desktop (in alto a sinistra) -->
                        <div
                            class="hidden md:flex absolute -top-6 -left-6 w-16 h-16 bg-lime-500 text-white rounded-2xl items-center justify-center text-3xl font-bold shadow-xl z-10">
                            1
                        </div>

                        <div class="flex items-start gap-3 md:block">
                            <div class="relative shrink-0">
                                <div
                                    class="bg-lime-100 w-12 h-12 md:w-20 md:h-20 rounded-lg md:rounded-2xl flex items-center justify-center md:mx-auto md:mb-4">
                                    <svg class="w-6 h-6 md:w-10 md:h-10 text-lime-700" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <!-- Badge numero mobile (piccolo in alto a destra dell'icona) -->
                                <div
                                    class="md:hidden absolute -top-1 -right-1 bg-lime-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                                    1</div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base md:text-2xl font-bold mb-2 md:mb-4 text-gray-900">Crea Gruppo</h3>
                                <ul
                                    class="list-disc list-inside space-y-1 md:space-y-1.5 text-sm md:text-base text-gray-900">
                                    <li>Registrati in 30 secondi</li>
                                    <li>Ottieni codice invito univoco</li>
                                    <li>Condividi via WhatsApp</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="bg-white p-3 md:p-10 rounded-xl md:rounded-3xl shadow-md hover:shadow-xl transition-all duration-500 border border-emerald-200 w-full md:relative">
                        <!-- Numero grande desktop (in alto a sinistra) -->
                        <div
                            class="hidden md:flex absolute -top-6 -left-6 w-16 h-16 bg-emerald-500 text-white rounded-2xl items-center justify-center text-3xl font-bold shadow-xl z-10">
                            2
                        </div>

                        <div class="flex items-start gap-3 md:block">
                            <div class="relative shrink-0">
                                <div
                                    class="bg-emerald-100 w-12 h-12 md:w-20 md:h-20 rounded-lg md:rounded-2xl flex items-center justify-center md:mx-auto md:mb-4">
                                    <svg class="w-6 h-6 md:w-10 md:h-10 text-emerald-700" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <!-- Badge numero mobile (piccolo in alto a destra dell'icona) -->
                                <div
                                    class="md:hidden absolute -top-1 -right-1 bg-emerald-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                                    2</div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base md:text-2xl font-bold mb-2 md:mb-4 text-gray-900">Dichiara
                                    Disponibilità</h3>
                                <ul
                                    class="list-disc list-inside space-y-1 md:space-y-1.5 text-sm md:text-base text-gray-900">
                                    <li>Scegli data disponibile</li>
                                    <li>Indica se puoi ospitare o partecipare</li>
                                    <li>Specifica numero massimo ospiti</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div
                        class="bg-white p-3 md:p-10 rounded-xl md:rounded-3xl shadow-md hover:shadow-xl transition-all duration-500 border border-lime-200 w-full md:relative">
                        <!-- Numero grande desktop (in alto a sinistra) -->
                        <div
                            class="hidden md:flex absolute -top-6 -left-6 w-16 h-16 bg-lime-500 text-white rounded-2xl items-center justify-center text-3xl font-bold shadow-xl z-10">
                            3
                        </div>

                        <div class="flex items-start gap-3 md:block">
                            <div class="relative shrink-0">
                                <div
                                    class="bg-lime-100 w-12 h-12 md:w-20 md:h-20 rounded-lg md:rounded-2xl flex items-center justify-center md:mx-auto md:mb-4">
                                    <svg class="w-6 h-6 md:w-10 md:h-10 text-lime-700" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                </div>
                                <!-- Badge numero mobile (piccolo in alto a destra dell'icona) -->
                                <div
                                    class="md:hidden absolute -top-1 -right-1 bg-lime-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                                    3</div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base md:text-2xl font-bold mb-2 md:mb-4 text-gray-900">Prenota e Goditi
                                </h3>
                                <ul
                                    class="list-disc list-inside space-y-1 md:space-y-1.5 text-sm md:text-base text-gray-900">
                                    <li>Visualizza disponibilità host</li>
                                    <li>Prenota con un click</li>
                                    <li>Ricevi notifiche automatiche</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pulsante navigazione -->
            <x-section-nav-button target="testimonials" direction="down" />
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials"
            class="snap-start snap-always relative min-h-screen h-auto flex flex-col
            justify-start md:justify-center pt-20 md:pt-2 pb-4 md:pb-16 px-4 sm:px-6
            lg:px-8 bg-linear-to-tr from-lime-200 to-emerald-50 overflow-hidden">
            <div class="max-w-7xl mx-auto w-full">
                <div class="text-center mb-3 md:mb-16">
                    <span
                        class="inline-block text-lime-600 font-semibold text-sm uppercase tracking-wider mb-2">Testimonianze</span>
                    <h2 class="text-xl sm:text-3xl md:text-5xl font-bold text-gray-900 mb-2 md:mb-4">
                        Amato da Gruppi in Tutta Italia
                    </h2>
                    <p class="text-sm md:text-xl text-gray-600 max-w-3xl mx-auto">
                        Centinaia di team hanno già trasformato le loro cene con DinnerTable
                    </p>
                </div>

                <div class="flex flex-col md:grid md:grid-cols-2 gap-3 md:gap-8">
                    @forelse ($reviews as $review)
                        <div
                            class="bg-linear-to-br from-lime-50 to-white p-3 md:p-8 rounded-xl md:rounded-3xl shadow-lg border border-lime-100">
                            @if ($review->comment)
                                <p
                                    class="text-xs md:text-base text-gray-700 leading-snug md:leading-relaxed mb-2 md:mb-4 italic">
                                    "{{ $review->comment }}"
                                </p>
                            @endif

                            <div class="flex items-center justify-between gap-2 md:gap-4 mb-2 md:mb-4">
                                <div class="flex gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            @svg('tabler-star-filled', 'w-3 h-3 md:w-4 md:h-4 text-yellow-400')
                                        @else
                                            @svg('tabler-star', 'w-3 h-3 md:w-4 md:h-4 text-gray-300')
                                        @endif
                                    @endfor
                                </div>

                                <div class='text-right'>
                                    <p class="text-xs md:text-base font-semibold text-gray-900">
                                        @svg('tabler-user', 'w-3 h-3 inline')
                                        {{ $review->user->name }}
                                    </p>
                                    <p class="text-[10px] md:text-sm text-gray-500">
                                        @if ($review->user->dinnerGroup)
                                            {{ $review->user->dinnerGroup->name }}
                                        @else
                                            {{ $review->created_at->locale('it')->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Fallback testimonial statico se non ci sono recensioni -->
                        <div
                            class="bg-linear-to-br from-lime-50 to-white p-3 md:p-8 rounded-xl md:rounded-3xl shadow-lg border border-lime-100">
                            <p
                                class="text-xs md:text-base text-gray-700 leading-snug md:leading-relaxed mb-2 md:mb-4 italic">
                                "App fantastica! Organizzare le cene non è mai stato così facile."
                            </p>
                            <div class="flex items-center justify-between gap-2 md:gap-4 mb-2 md:mb-4">
                                <div class="flex gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @svg('tabler-star-filled', 'w-3 h-3 md:w-4 md:h-4 text-yellow-400')
                                    @endfor
                                </div>
                                <div class='text-right'>
                                    <p class="text-xs md:text-base font-semibold text-gray-900">
                                        @svg('tabler-user', 'w-3 h-3 inline') Utente DinnerTable
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pulsante navigazione -->
            <x-section-nav-button target="cta" direction="down" />
        </section>

        <!-- CTA Section -->
        <section id="cta"
            class="snap-start snap-always relative min-h-screen h-auto flex flex-col justify-start md:justify-center pt-20 md:pt-2 pb-4 md:pb-16 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-500 via-lime-400 to-emerald-400 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute top-10 left-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative max-w-5xl mx-auto text-center">
                <!-- Chef Icon -->
                @svg('tabler-chef-hat-filled', 'w-32 h-32 inline text-white -rotate-15')

                <h2
                    class="text-4xl sm:text-6xl bg-clip-text text-transparent bg-linear-to-r from-emerald-900 to-lime-700 font-extrabold mb-3 md:mb-6 leading-tight">
                    Pronto a Semplificare Le Tue Cene?
                </h2>
                <p class="text-sm sm:text-xl text-lime-700 mb-2 md:mb-4 max-w-2xl mx-auto">
                    Unisciti a centinaia di gruppi che organizzano cene senza stress, e scopri come può aiutarti a
                    risparmiare tempo e a migliorare la tua esperienza culinaria.
                </p>
                <p class="text-xs sm:text-base text-lime-900 mb-4 md:mb-6">
                    Registrati gratis in 30 secondi • Nessuna carta richiesta • Nessun costo nascosto
                </p>

                <a href="/dinner/register"
                    class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-lime-600 px-8 md:px-12 py-3 md:py-5 rounded-full text-lg md:text-2xl font-bold hover:bg-gray-50 hover:scale-105 hover:shadow-[0_20px_60px_rgba(0,0,0,0.3)] transition-all duration-300 shadow-2xl">
                    <span>Inizia Ora</span>
                    @svg('tabler-arrow-big-right-lines-filled', 'h-8 w-8 inline')
                </a>

                <div class="mt-4 md:mt-8 flex  justify-center gap-3 sm:gap-6 text-white text-xs sm:text-sm">
                    <span>@svg('tabler-shield-filled', 'h-4 w-4 inline') Gratis per sempre</span>
                    <span>@svg('tabler-clock', 'h-4 w-4 inline') Setup in 30 secondi</span>
                    <span>@svg('tabler-bolt', 'h-4 w-4 inline') Supporto dedicato</span>
                </div>
            </div>

            <!-- Pulsante navigazione -->
            <x-section-nav-button target="contacts" direction="down" />
        </section>

        <!-- Sezione Contatti -->
        <section id="contacts"
            class="snap-start snap-always bg-gray-900 text-white min-h-screen h-auto flex items-start md:items-center pt-20 md:pt-2 pb-4 md:pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-10 ">
                    <div class="col-span-2">
                        <div class="flex items-center gap-3 mb-1">
                            <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable" class="h-12 w-auto">
                            <h3 class="text-2xl font-bold text-lime-400">DinnerTable</h3>
                        </div>
                        <p class="text-gray-400 mb-6 leading-relaxed max-w-md">
                            La piattaforma intelligente che trasforma il caos della pianificazione in momenti di pura
                            convivialità.
                        </p>
                        <div class="flex gap-2">
                            @svg('tabler-brand-instagram', 'h-10 w-10 p-2 inline  rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors')
                            @svg('tabler-brand-facebook', 'h-10 w-10 p-2 inline  rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors')
                            @svg('tabler-brand-whatsapp', 'h-10 w-10 p-2 inline  rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors')
                            @svg('tabler-brand-telegram', 'h-10 w-10 p-2 inline  rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors')
                            @svg('tabler-brand-messenger', 'h-10 w-10 p-2 inline  rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors')


                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-4 text-lime-400">Navigazione</h4>
                        <ul class="space-y-2">
                            <li><a href="#benefits" class="text-gray-400 hover:text-lime-400 transition">Vantaggi</a>
                            </li>
                            <li><a href="#features"
                                    class="text-gray-400 hover:text-lime-400 transition">Funzionalità</a></li>
                            <li><a href="#testimonials"
                                    class="text-gray-400 hover:text-lime-400 transition">Testimonianze</a></li>
                            <li><a href="/dinner/register"
                                    class="text-lime-50 hover:text-lime-300 transition">Registrati
                                    Gratis</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-4 text-lime-400">Supporto</h4>
                        <ul class="space-y-2">
                            <li><a href="/dinner" class="text-lime-50 hover:text-lime-300 transition">Pannello
                                    Utente</a></li>
                            <li><a href="/admin" class="text-gray-400 hover:text-lime-400 transition">Pannello
                                    Admin</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-lime-400 transition">Centro
                                    Assistenza</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-lime-400 transition">Contattaci</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 col-span-full">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-gray-400 text-sm">
                            &copy; {{ date('Y') }} DinnerTable. Tutti i diritti riservati. Fatto con ❤️ in casa
                        </p>
                        <div class="flex gap-6 text-sm text-gray-400">
                            <a href="#" class="hover:text-lime-400 transition">Privacy Policy</a>
                            <a href="#" class="hover:text-lime-400 transition">Termini di Servizio</a>
                            <a href="#" class="hover:text-lime-400 transition">Cookie Policy</a>
                        </div>
                    </div>
                </div>
            </div>
            <x-section-nav-button target="hero" direction="up" />
        </section>
    </main>

    @vite('resources/js/app.js')

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });

                // Close menu when clicking on a link
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                    });
                });
            }

            // Smooth fade-in/out SOLO per le sezioni principali
            const observerOptions = {
                threshold: [0.1, 0.5, 0.9],
                rootMargin: '0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const section = entry.target;

                    if (entry.isIntersecting && entry.intersectionRatio > 0.3) {
                        // Fade in when entering
                        section.classList.add('fade-in-visible');
                        section.classList.remove('fade-out-visible');
                        section.style.opacity = 1;
                    } else if (!entry.isIntersecting) {
                        // Fade out when completely out of view
                        section.classList.remove('fade-in-visible');
                        section.classList.add('fade-out-visible');
                        section.style.opacity = 0.3;
                    }
                });
            }, observerOptions);

            // Observe SOLO le sezioni principali
            document.querySelectorAll('section').forEach((section) => {
                section.classList.add('fade-in');
                observer.observe(section);
            });

            // Smooth scroll to sections using native snap scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

    <style>
        /* Snap scrolling configuration */
        html,
        body {
            scroll-behavior: smooth;
            overscroll-behavior-y: none;
        }

        main {
            scroll-padding-top: 5rem;
            /* 80px = altezza navbar */
        }

        /* Nascondi scrollbar */
        main::-webkit-scrollbar {
            display: none;
        }

        main {
            -ms-overflow-style: none;
            /* IE e Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* Fade-in/out animation SOLO per sezioni - senza will-change */
        section,
        footer {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fade-in {
            opacity: 0;
        }

        .fade-in-visible {
            opacity: 1 !important;
        }

        .fade-out-visible {
            opacity: 0.3 !important;
        }

        /* Smoother transitions for all interactive elements */
        a,
        button {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Disable default smooth scroll behavior - we handle it manually */
        html,
        * {
            scroll-behavior: auto;
        }

        /* Blob animations */
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(20px, -50px) scale(1.1);
            }

            50% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            75% {
                transform: translate(50px, 50px) scale(1.05);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>

</html>
