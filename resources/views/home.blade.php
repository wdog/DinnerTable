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
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable" class="h-10 w-auto">
                    <span class="text-xl font-bold text-gray-900">DinnerTable</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#hero" class="text-gray-600 hover:text-lime-600 transition font-medium">Home</a>
                    <a href="#benefits" class="text-gray-600 hover:text-lime-600 transition font-medium">Vantaggi</a>
                    <a href="#features" class="text-gray-600 hover:text-lime-600 transition font-medium">Funzionalità</a>
                    <a href="#testimonials" class="text-gray-600 hover:text-lime-600 transition font-medium">Testimonianze</a>
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
        <!-- Hero Section -->
        <section id="hero" class="relative min-h-screen flex flex-col justify-center pt-32 pb-24 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-lime-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-lime-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

            <div class="relative max-w-7xl mx-auto">
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 bg-lime-100 text-lime-800 px-4 py-2 rounded-full text-sm font-semibold mb-8 shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        100% Gratuito · Nessuna Carta Richiesta
                    </div>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight tracking-tight">
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
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="#features"
                            class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-4 rounded-full text-lg font-semibold border-2 border-gray-200 hover:border-lime-500 hover:text-lime-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Guarda Come Funziona</span>
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Setup in 2 minuti</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">100% Gratis</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Nessun vincolo</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Image/Illustration -->
                <div class="mt-16 relative">
                    <div class="absolute inset-0 bg-linear-to-tl from-white/40 via-transparent to-transparent z-10"></div>
                    <img src="{{ asset('images/banner_dinner.png') }}" alt="DinnerTable Interface"
                         class="mx-auto max-w-4xl w-full rounded-2xl shadow-2xl">
                </div>
            </div>

            <!-- Scroll Down Indicator -->
            <a href="#benefits" class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-gray-400 hover:text-lime-600 transition-colors animate-bounce">
                <span class="text-sm font-medium">Scopri di più</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </a>
        </section>

        <!-- Benefits Section -->
        <section id="benefits" class="relative min-h-screen flex flex-col justify-center py-24 px-4 sm:px-6 lg:px-8 bg-white">
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

            <!-- Next Section Button -->
            <a href="#features" class="mx-auto mt-16 flex w-fit items-center gap-3 bg-lime-100 text-lime-700 px-6 py-3 rounded-full hover:bg-lime-200 transition-all duration-300 group shadow-md hover:shadow-lg">
                <span class="font-semibold">Prossima sezione</span>
                <svg class="w-6 h-6 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </a>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative min-h-screen flex flex-col justify-center py-24 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-50 via-white to-emerald-50">
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

            <!-- Navigation Buttons -->
            <div class="flex justify-center items-center gap-8 mt-16">
                <a href="#benefits" class="flex items-center gap-3 bg-white text-gray-700 px-6 py-3 rounded-full hover:bg-gray-50 transition-all duration-300 group shadow-md hover:shadow-lg border border-gray-200">
                    <svg class="w-6 h-6 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span class="font-semibold">Sezione precedente</span>
                </a>
                <a href="#testimonials" class="flex items-center gap-3 bg-lime-100 text-lime-700 px-6 py-3 rounded-full hover:bg-lime-200 transition-all duration-300 group shadow-md hover:shadow-lg">
                    <span class="font-semibold">Prossima sezione</span>
                    <svg class="w-6 h-6 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="relative min-h-screen flex flex-col justify-center py-24 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <span class="inline-block text-lime-600 font-semibold text-sm uppercase tracking-wider mb-3">Testimonianze</span>
                    <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                        Amato da Gruppi in Tutta Italia
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Centinaia di team hanno già trasformato le loro cene con DinnerTable
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-linear-to-br from-lime-50 to-white p-8 rounded-3xl shadow-lg border border-lime-100">
                        <div class="flex gap-1 mb-4">
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 leading-relaxed mb-6 italic">
                            "Finalmente niente più caos su WhatsApp! Con DinnerTable organizziamo le nostre cene settimanali in 5 minuti. Fantastico!"
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-linear-to-br from-lime-400 to-emerald-400 flex items-center justify-center text-white font-bold text-lg">
                                MC
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Marco Colombo</p>
                                <p class="text-sm text-gray-500">Team Leader @ Milano</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-linear-to-br from-emerald-50 to-white p-8 rounded-3xl shadow-lg border border-emerald-100">
                        <div class="flex gap-1 mb-4">
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 leading-relaxed mb-6 italic">
                            "Semplicissimo da usare! Il nostro gruppo di 8 amici non può più farne a meno. Le notifiche automatiche sono comodissime."
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-linear-to-br from-emerald-400 to-lime-400 flex items-center justify-center text-white font-bold text-lg">
                                GR
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Giulia Rossi</p>
                                <p class="text-sm text-gray-500">Organizzatrice @ Roma</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-linear-to-br from-lime-50 to-white p-8 rounded-3xl shadow-lg border border-lime-100">
                        <div class="flex gap-1 mb-4">
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-5 h-5 text-lime-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 leading-relaxed mb-6 italic">
                            "Perfetto per il nostro team aziendale! Ora le cene mensali sono sempre ben organizzate e tutti partecipano volentieri."
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-linear-to-br from-lime-400 to-emerald-400 flex items-center justify-center text-white font-bold text-lg">
                                AF
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Alessandro Ferrari</p>
                                <p class="text-sm text-gray-500">HR Manager @ Torino</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-center items-center gap-8 mt-16">
                <a href="#features" class="flex items-center gap-3 bg-white text-gray-700 px-6 py-3 rounded-full hover:bg-gray-50 transition-all duration-300 group shadow-md hover:shadow-lg border border-gray-200">
                    <svg class="w-6 h-6 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span class="font-semibold">Sezione precedente</span>
                </a>
                <a href="#cta" class="flex items-center gap-3 bg-lime-500 text-white px-6 py-3 rounded-full hover:bg-lime-600 transition-all duration-300 group shadow-lg hover:shadow-xl">
                    <span class="font-semibold">Inizia ora</span>
                    <svg class="w-6 h-6 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>
            </div>
        </section>

        <!-- CTA Section -->
        <section id="cta" class="relative min-h-screen flex flex-col justify-center py-32 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-lime-500 via-lime-400 to-emerald-400 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute top-10 left-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl font-extrabold text-white mb-6 leading-tight">
                    Pronto a Semplificare<br>
                    Le Tue Cene di Gruppo?
                </h2>
                <p class="text-2xl text-lime-50 mb-4 max-w-3xl mx-auto">
                    Unisciti a centinaia di gruppi che hanno già scelto DinnerTable
                </p>
                <p class="text-lg text-white/90 mb-12 max-w-2xl mx-auto">
                    Registrazione gratuita • Nessuna carta richiesta • Cancella quando vuoi
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="/dinner/register"
                        class="group inline-flex items-center gap-3 bg-white text-lime-600 px-12 py-5 rounded-full text-2xl font-bold hover:bg-gray-50 transition-all duration-300 shadow-2xl hover:shadow-white/50 hover:scale-105">
                        <span>Inizia Gratis Ora</span>
                        <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>

                <div class="mt-12 flex flex-wrap justify-center items-center gap-8 text-white">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">Nessun impegno</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">Gratis per sempre</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">Supporto incluso</span>
                    </div>
                </div>
            </div>

            <!-- Back to Top -->
            <a href="#hero" class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/80 hover:text-white transition-colors group">
                <svg class="w-6 h-6 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                <span class="text-sm font-medium">Torna su</span>
            </a>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-10 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo-small.png') }}" alt="DinnerTable" class="h-12 w-auto">
                        <h3 class="text-2xl font-bold text-lime-400">DinnerTable</h3>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed max-w-md">
                        La piattaforma intelligente che trasforma il caos della pianificazione in momenti di pura convivialità.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 hover:bg-lime-500 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-lime-400">Navigazione</h4>
                    <ul class="space-y-3">
                        <li><a href="#benefits" class="text-gray-400 hover:text-lime-400 transition">Vantaggi</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-lime-400 transition">Funzionalità</a></li>
                        <li><a href="#testimonials" class="text-gray-400 hover:text-lime-400 transition">Testimonianze</a></li>
                        <li><a href="/dinner/register" class="text-lime-400 hover:text-lime-300 transition font-semibold">Registrati Gratis</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-lime-400">Supporto</h4>
                    <ul class="space-y-3">
                        <li><a href="/dinner" class="text-gray-400 hover:text-lime-400 transition">Pannello Utente</a></li>
                        <li><a href="/admin" class="text-gray-400 hover:text-lime-400 transition">Pannello Admin</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-lime-400 transition">Centro Assistenza</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-lime-400 transition">Contattaci</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">
                        &copy; {{ date('Y') }} DinnerTable. Tutti i diritti riservati. Fatto con ❤️ in Italia
                    </p>
                    <div class="flex gap-6 text-sm text-gray-400">
                        <a href="#" class="hover:text-lime-400 transition">Privacy Policy</a>
                        <a href="#" class="hover:text-lime-400 transition">Termini di Servizio</a>
                        <a href="#" class="hover:text-lime-400 transition">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Back to Top Button -->
    <a href="#hero" id="backToTop"
       class="fixed bottom-8 right-8 bg-lime-500 text-white w-14 h-14 rounded-full shadow-2xl shadow-lime-500/30 flex items-center justify-center hover:bg-lime-600 transition-all duration-300 hover:scale-110 opacity-0 invisible z-40">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </a>

    @vite('resources/js/app.js')

    <script>
        // Show/hide back to top button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
            }
        });

        // Smooth fade-in on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-visible');
                }
            });
        }, observerOptions);

        // Observe all sections and cards
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('section').forEach(section => {
                section.classList.add('fade-in');
                observer.observe(section);
            });

            // Observe cards and important elements
            document.querySelectorAll('.group').forEach(el => {
                el.classList.add('fade-in');
                observer.observe(el);
            });
        });
    </script>

    <style>
        /* Smooth scroll with offset for fixed navbar */
        html {
            scroll-padding-top: 80px;
        }

        /* Fade-in animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in-visible {
            opacity: 1;
            transform: translateY(0);
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
