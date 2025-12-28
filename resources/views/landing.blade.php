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

<body class="bg-white antialiased overflow-hidden">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-md shadow-sm z-50 border-b border-lime-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable" class="h-10 w-auto">
                    <span class="text-xl font-bold text-gray-900">DinnerTable</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#hero" class="nav-link text-gray-600 hover:text-lime-600 transition font-medium">Home</a>
                    <a href="#benefits" class="nav-link text-gray-600 hover:text-lime-600 transition font-medium">Vantaggi</a>
                    <a href="#features" class="nav-link text-gray-600 hover:text-lime-600 transition font-medium">Funzionalità</a>
                    <a href="#testimonials" class="nav-link text-gray-600 hover:text-lime-600 transition font-medium">Testimonianze</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/dinner" class="text-gray-600 hover:text-lime-600 transition font-medium">Accedi</a>
                    <a href="/dinner/register"
                        class="bg-lime-500 text-white px-6 py-2.5 rounded-full hover:bg-lime-600 transition-all duration-300 font-semibold shadow-lg shadow-lime-500/30 hover:shadow-xl hover:shadow-lime-500/40 hover:scale-105">
                        Inizia Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- Contenitore delle sezioni -->
        <div id="sections-container"
            class="flex flex-col h-screen overflow-y-scroll snap-y snap-mandatory scroll-smooth">
            <!-- Hero Section - Part 1: Text & CTA -->
            <section id="hero"
                class="snap-start relative min-h-screen flex flex-col justify-center pt-24 pb-16 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50 overflow-hidden">
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

                <div class="relative max-w-7xl mx-auto w-full">
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
                            class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight tracking-tight">
                            Organizza Cene di Gruppo
                            <br>
                            <span class="bg-clip-text text-transparent bg-linear-to-r from-lime-600 to-emerald-600">
                                Senza Stress
                            </span>
                        </h1>

                        <p class="text-xl sm:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                            La piattaforma intelligente che trasforma il caos dell'organizzazione
                            in momenti di pura convivialità. Coordina turni, prenota posti e goditi la cena.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                            <a href="/dinner/register"
                                class="group inline-flex items-center gap-3 bg-lime-500 text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-lime-600 transition-all duration-300 shadow-2xl shadow-lime-500/30 hover:shadow-lime-500/50 hover:scale-105">
                                <span>Inizia Gratis Ora</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="#features"
                                class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-4 rounded-full text-lg font-semibold border-2 border-gray-200 hover:border-lime-500 hover:text-lime-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Guarda Come Funziona</span>
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Setup in 2 minuti</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">100% Gratis</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Nessun vincolo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll Down Indicator -->
                <a href="#hero-image"
                    class="absolute bottom-8 left-1/2 -translate-x-1/2 flex border border-gray-300 rounded-full p-2 w-32
                    bg-gray-500/20 justify-center items-center gap-2 text-gray-400
                    hover:text-lime-600 transition-colors animate-bounce">
                    <span class="text-sm font-medium">Scopri di più</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </section>

            <!-- Hero Section - Part 2: Image -->
            <section id="hero-image"
                class="snap-start relative min-h-screen flex flex-col justify-center items-center py-16 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-white via-emerald-50 to-lime-50 overflow-hidden">
                <div class="relative max-w-7xl mx-auto w-full">
                    <!-- Hero Image/Illustration -->
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-white/40 via-transparent to-transparent z-10">
                        </div>
                        <img src="{{ asset('images/banner_dinner.png') }}" alt="DinnerTable Interface"
                            class="mx-auto max-w-4xl w-full rounded-2xl shadow-2xl">
                    </div>
                </div>

                <!-- Scroll Down Indicator -->
                <a href="#section-2"
                    class="absolute bottom-8 left-1/2 -translate-x-1/2 flex border border-gray-300 rounded-full p-2 w-32
                    bg-gray-500/20 justify-center items-center gap-2 text-gray-400
                    hover:text-lime-600 transition-colors animate-bounce">
                    <span class="text-sm font-medium">Continua</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </section>

            <!-- Benefits Section -->
            <section id="benefits" class="snap-start relative min-h-screen flex flex-col justify-center py-24 px-4 sm:px-6 lg:px-8 bg-white">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <span class="inline-block text-lime-600 font-semibold text-sm uppercase tracking-wider mb-3">Vantaggi</span>
                        <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                            Perché Scegliere DinnerTable?
                        </h2>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Più di una semplice agenda: il tuo assistente intelligente per cene perfette
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- Benefit 1 -->
                        <div class="group relative bg-linear-to-br from-lime-50 to-emerald-50 p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-lime-100 hover:border-lime-300">
                            <div class="absolute top-4 right-4 w-12 h-12 bg-lime-200 rounded-full opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative">
                                <div class="bg-lime-100 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 text-gray-900">Risparmia Tempo Prezioso</h3>
                                <p class="text-gray-600 leading-relaxed mb-4">
                                    Basta perdere ore in chat infinite per capire chi può ospitare. Visualizza tutte le disponibilità in un calendario intelligente e prenota in un click.
                                </p>
                                <div class="flex items-center text-lime-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                    <span>Scopri di più</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Benefit 2 -->
                        <div class="group relative bg-linear-to-br from-lime-50 to-emerald-50 p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-lime-100 hover:border-lime-300">
                            <div class="absolute top-4 right-4 w-12 h-12 bg-emerald-200 rounded-full opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative">
                                <div class="bg-emerald-100 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 text-gray-900">Zero Stress Organizzativo</h3>
                                <p class="text-gray-600 leading-relaxed mb-4">
                                    Il sistema gestisce automaticamente prenotazioni, previene sovrapposizioni e ti notifica quando è il tuo turno. Tu pensa solo al menu!
                                </p>
                                <div class="flex items-center text-emerald-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                    <span>Scopri di più</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Benefit 3 -->
                        <div class="group relative bg-linear-to-br from-lime-50 to-emerald-50 p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-lime-100 hover:border-lime-300">
                            <div class="absolute top-4 right-4 w-12 h-12 bg-lime-200 rounded-full opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative">
                                <div class="bg-lime-100 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-3 text-gray-900">Rafforza i Legami</h3>
                                <p class="text-gray-600 leading-relaxed mb-4">
                                    Le cene regolari creano tradizioni e ricordi indimenticabili. Mantieni viva la socialità del gruppo senza il peso della burocrazia.
                                </p>
                                <div class="flex items-center text-lime-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                    <span>Scopri di più</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll Down Indicator -->
                <a href="#features"
                    class="absolute bottom-8 left-1/2 -translate-x-1/2 flex border border-gray-300 rounded-full p-2 w-32
                    bg-gray-500/20 justify-center items-center gap-2 text-gray-400
                    hover:text-lime-600 transition-colors animate-bounce">
                    <span class="text-sm font-medium">Continua</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </section>

            <!-- Features Section -->
            <section id="features" class="snap-start relative min-h-screen flex flex-col justify-center py-24 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <span class="inline-block text-lime-600 font-semibold text-sm uppercase tracking-wider mb-3">Come Funziona</span>
                        <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                            Semplice Come 1, 2, 3
                        </h2>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Inizia a organizzare le tue cene di gruppo in pochi minuti, come un professionista
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                        <!-- Step 1 -->
                        <div class="relative group">
                            <div class="absolute -top-6 -left-6 w-16 h-16 bg-lime-500 text-white rounded-2xl flex items-center justify-center text-3xl font-bold shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                                1
                            </div>
                            <div class="relative bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 h-full border-2 border-transparent hover:border-lime-200">
                                <div class="bg-linear-to-br from-lime-100 to-emerald-100 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-10 h-10 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-gray-900 text-center">Crea il Tuo Gruppo</h3>
                                <p class="text-gray-600 leading-relaxed mb-4 text-center">
                                    Registrati in 30 secondi e crea un nuovo gruppo. Oppure unisciti a uno esistente con il codice di invito.
                                </p>
                                <div class="bg-lime-50 rounded-xl p-4 border border-lime-200">
                                    <p class="text-sm text-gray-700 italic text-center">
                                        <svg class="w-4 h-4 inline text-lime-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Codice univoco condivisibile via WhatsApp
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative group">
                            <div class="absolute -top-6 -left-6 w-16 h-16 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-3xl font-bold shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                                2
                            </div>
                            <div class="relative bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 h-full border-2 border-transparent hover:border-emerald-200">
                                <div class="bg-linear-to-br from-emerald-100 to-lime-100 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-10 h-10 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-gray-900 text-center">Dichiara Disponibilità</h3>
                                <p class="text-gray-600 leading-relaxed mb-4 text-center">
                                    Visualizza il calendario e indica quando puoi ospitare. Scegli data, orario e numero di ospiti.
                                </p>
                                <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200">
                                    <p class="text-sm text-gray-700 italic text-center">
                                        <svg class="w-4 h-4 inline text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Gestione automatica sovrapposizioni
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative group">
                            <div class="absolute -top-6 -left-6 w-16 h-16 bg-lime-500 text-white rounded-2xl flex items-center justify-center text-3xl font-bold shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                                3
                            </div>
                            <div class="relative bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 h-full border-2 border-transparent hover:border-lime-200">
                                <div class="bg-linear-to-br from-lime-100 to-emerald-100 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-10 h-10 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold mb-4 text-gray-900 text-center">Prenota e Goditi</h3>
                                <p class="text-gray-600 leading-relaxed mb-4 text-center">
                                    Esplora le disponibilità del gruppo e prenota il tuo posto con un click. Ricevi notifiche e promemoria.
                                </p>
                                <div class="bg-lime-50 rounded-xl p-4 border border-lime-200">
                                    <p class="text-sm text-gray-700 italic text-center">
                                        <svg class="w-4 h-4 inline text-lime-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Sincronizzazione in tempo reale
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll Down Indicator -->
                <a href="#testimonials"
                    class="absolute bottom-8 left-1/2 -translate-x-1/2 flex border border-gray-300 rounded-full p-2 w-32
                    bg-gray-500/20 justify-center items-center gap-2 text-gray-400
                    hover:text-lime-600 transition-colors animate-bounce">
                    <span class="text-sm font-medium">Continua</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </section>

            <!-- Testimonials Section -->
            <section id="testimonials" class="snap-start flex items-center justify-center bg-yellow-200 min-h-screen">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-yellow-900">Sezione Finale</h1>
                    <p class="mt-4 text-xl text-yellow-800">Grazie per aver visitato la nostra pagina!</p>
                </div>
            </section>
        </div>
    </main>



    @vite('resources/js/app.js')

    <script>
        // Intersection Observer for fade animations
        const observerOptions = {
            threshold: [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1],
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const section = entry.target;

                if (entry.isIntersecting) {
                    // Fade in when entering
                    section.classList.add('fade-in-visible');
                    section.classList.remove('fade-out-visible');

                    // Calculate opacity based on intersection ratio for smooth transition
                    const opacity = Math.min(entry.intersectionRatio * 1.5, 1);
                    section.style.opacity = opacity;
                } else {
                    // Fade out when leaving
                    section.classList.remove('fade-in-visible');
                    section.classList.add('fade-out-visible');
                    section.style.opacity = 0.3;
                }
            });
        }, observerOptions);

        // Observe all sections and cards
        document.addEventListener('DOMContentLoaded', () => {
            // Observe sections with heavy fade
            document.querySelectorAll('section').forEach((section, index) => {
                section.classList.add('fade-in');
                observer.observe(section);
            });

            // Observe cards with stagger effect
            document.querySelectorAll('.group').forEach((el, index) => {
                el.classList.add('fade-in');
                el.style.transitionDelay = `${(index % 3) * 0.3}s`;
                observer.observe(el);
            });

            // Observe navigation buttons
            document.querySelectorAll('a[href^="#"]').forEach(el => {
                if (el.closest('.flex.justify-center')) {
                    el.classList.add('fade-in');
                    observer.observe(el);
                }
            });
        });

        // Slow down scroll speed with smooth animation
        const sectionsContainer = document.getElementById('sections-container');

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement && sectionsContainer) {
                    const startPosition = sectionsContainer.scrollTop;
                    const targetPosition = targetElement.offsetTop;
                    const distance = targetPosition - startPosition;
                    const duration = 1500; // 1.5 seconds for faster scroll
                    let start = null;

                    function animation(currentTime) {
                        if (start === null) start = currentTime;
                        const timeElapsed = currentTime - start;
                        const progress = Math.min(timeElapsed / duration, 1);

                        // Cubic easing for smooth movement
                        const easing = progress < 0.5
                            ? 4 * progress * progress * progress
                            : 1 - Math.pow(-2 * progress + 2, 3) / 2;

                        sectionsContainer.scrollTop = startPosition + (distance * easing);

                        if (timeElapsed < duration) {
                            requestAnimationFrame(animation);
                        }
                    }

                    requestAnimationFrame(animation);
                }
            });
        });
    </script>

    <style>
        /* Smooth scroll with offset for fixed navbar */
        html {
            scroll-padding-top: 80px;
        }

        /* Faster fade-in/out animation for sections */
        section {
            transition: opacity 1.2s cubic-bezier(0.25, 0.1, 0.25, 1),
                        transform 1.2s cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(60px) scale(0.95);
        }

        .fade-in-visible {
            opacity: 1 !important;
            transform: translateY(0) scale(1);
        }

        .fade-out-visible {
            opacity: 0.3 !important;
            transform: translateY(-30px) scale(0.98);
        }

        /* Cards stagger animation */
        .group {
            transition: opacity 1.0s cubic-bezier(0.25, 0.1, 0.25, 1),
                        transform 1.0s cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        /* Smoother transitions for all interactive elements */
        a, button {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Disable default smooth scroll behavior - we handle it manually */
        html, * {
            scroll-behavior: auto;
        }

        /* Blob animations */
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -50px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
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
