{{--
    Pagina ViewNotifications - Visualizzazione notifiche utente

    Features:
    - Header con conteggio notifiche non lette
    - Filtro toggle "Mostra tutti" / "Mostra solo nuovi"
    - Pulsanti azione: Segna tutte lette, Elimina tutte
    - Card notifiche color-coded per tipo (success, danger, info, warning)
    - Dark mode support tramite light-dark() CSS function
    - Opacity 0.6 per notifiche lette
    - Paginazione 5 per pagina

    @see App\Filament\App\Pages\ViewNotifications
--}}
<x-filament-panels::page>

    <x-filament::section>

        <div class="space-y-4">
            {{-- Header con azioni --}}
            <div class="flex justify-between items-center">
                {{-- Titolo con conteggio non lette --}}
                <h2 class="text-xl font-semibold text-slate-900  dark:text-slate-100">
                    Nuovi Messaggi
                    {{ auth()->user()->unreadNotifications->count() > 0
                        ? '(' . auth()->user()->unreadNotifications->count() . ')'
                        : '' }}
                </h2>

                {{-- Pulsanti azione --}}
                <div class="flex gap-2">
                    {{-- Toggle filtro: colore info se attivo, gray se disattivo --}}
                    <x-filament::button wire:click="toggleUnreadFilter" :color="$showOnlyUnread ? 'info' : 'gray'" size="sm"
                        :outlined="!$showOnlyUnread">
                        @svg('tabler-filter', 'w-4 h-4 mr-1')
                        {{ $showOnlyUnread ? 'Mostra tutti' : 'Mostra solo nuovi' }}
                    </x-filament::button>

                    {{-- Segna tutte come lette (solo se ci sono non lette) --}}
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <x-filament::button wire:click="markAllAsRead" color="primary" size="sm">
                            @svg('tabler-check', 'w-4 h-4 mr-1')
                            Segna come letti
                        </x-filament::button>
                    @endif

                    {{-- Elimina tutte (solo se ci sono notifiche) --}}
                    @if (auth()->user()->notifications->count() > 0)
                        <x-filament::button wire:click="deleteAll" color="danger" size="sm">
                            @svg('tabler-trash', 'w-4 h-4 mr-1')
                            Elimina tutti
                        </x-filament::button>
                    @endif
                </div>
            </div>

            {{-- Lista notifiche --}}
            @forelse ($this->getNotifications() as $notification)
                @php
                    $data = $notification->data;
                    $icon = $data['icon'] ?? 'tabler-bell';
                    $color = $data['color'] ?? 'gray'; // success, danger, info, warning, gray

                    // Opacity: lette 0.6, non lette 1
                    $opacity = $notification->read_at ? '0.6' : '1';
                @endphp

                {{--
                    Card notifica con colore dinamico
                    - CSS custom properties: --bg-light e --bg-dark
                    - light-dark() function per supporto dark mode
                    - Light: usa -200 (chiaro)
                    - Dark: usa -900 (scuro)
                --}}
                <div class="rounded-lg p-3 shadow hover:shadow-lg transition-all duration-200
                text-slate-900  dark:text-slate-100"
                    style="
                        --bg-light: var(--{{ $color }}-200);
                        --bg-dark: var(--{{ $color }}-900);
                        background-color: light-dark(var(--bg-light), var(--bg-dark));
                        opacity: {{ $opacity }};
                    ">

                    <div class="flex items-start justify-between gap-3">
                        {{-- Icona e contenuto --}}
                        <div class="flex items-start gap-2 flex-1 min-w-0">
                            {{-- Icona --}}
                            <div class="shrink-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-900/20 dark:bg-slate-100/50 flex items-center justify-center">
                                    @svg($icon, 'w-4 h-4 text-slate-900')
                                </div>
                            </div>

                            {{-- Contenuto --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2">

                                    <h3 class="text-md font-semibold truncate ">
                                        {{ $data['title'] ?? 'Notifica' }}
                                    </h3>

                                    @if (!$notification->read_at)
                                        <span
                                            class="text-lx px-1.5 py-0.5 rounded bg-white/90 text-slate-900 font-medium">
                                            Nuovo
                                        </span>
                                    @endif
                                </div>

                                @if (isset($data['body']))
                                    <p class="mt-0.5 text-sm text-slate-900/80 dark:text-slate-100/80 line-clamp-2">
                                        {{ $data['body'] }}
                                    </p>
                                @endif


                            </div>
                        </div>

                        <div>
                            <p class="mt-1 text-sm text-slate-900 dark:text-slate-100 flex items-center gap-1">
                                @svg('tabler-clock', 'w-3 h-3')
                                <span class="truncate">{{ $notification->created_at->diffForHumans() }}</span>
                                @if ($notification->read_at)
                                    <span class="text-slate-900 dark:text-slate-100 font-bold">· Letta</span>
                                @endif
                            </p>
                        </div>

                        {{-- Azioni --}}
                        <div class="flex items-center gap-4 justify-center shrink-0 ">
                            @if (!$notification->read_at)
                                <x-filament::icon-button icon="tabler-check" size="sm"
                                    wire:click="markAsRead('{{ $notification->id }}')" tooltip="Segna come letta"
                                    class="bg-white/60! hover:bg-lime-500! text-slate-900" />
                            @endif

                            <x-filament::icon-button icon="tabler-trash" size="sm"
                                wire:click="deleteNotification('{{ $notification->id }}')" tooltip="Elimina"
                                class="bg-white/60! hover:bg-red-500! text-slate-900" />
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        @svg('tabler-bell-off', 'w-8 h-8 text-gray-400')
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-1">
                        Nessuna notifica
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Quando riceverai notifiche, appariranno qui
                    </p>
                </div>
            @endforelse

            {{-- Paginazione --}}
            <div class="mt-4">
                {{ $this->getNotifications()->links() }}
            </div>
        </div>

    </x-filament::section>

</x-filament-panels::page>
