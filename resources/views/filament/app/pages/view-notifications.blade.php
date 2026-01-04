<x-filament-panels::page>

    <x-filament::section>

        <div class="space-y-4">
            {{-- Header con azioni --}}
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                    Tutte le notifiche
                </h2>

                <div>

                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <x-filament::button wire:click="markAllAsRead" color="primary" size="sm">
                            @svg('tabler-check', 'w-4 h-4 mr-1')
                            Segna tutte come lette
                        </x-filament::button>
                    @endif

                    @if (auth()->user()->notifications->count() > 0)
                        <x-filament::button wire:click="deleteAll" color="danger" size="sm">
                            @svg('tabler-trash', 'w-4 h-4 mr-1')
                            Elimina tutte
                        </x-filament::button>
                    @endif
                </div>
            </div>

            {{-- Lista notifiche --}}
            @forelse($this->getNotifications() as $notification)
                @php
                    $data = $notification->data;
                    $icon = $data['icon'] ?? 'tabler-bell';
                    $color = $data['color'] ?? 'gray';

                    // Usa CSS custom properties definite nel panel per colori dinamici
                    $bgColorVar = 'var(--' . $color . '-200)';
                    $opacity = $notification->read_at ? '0.5' : '1';
                @endphp

                <div class="rounded-lg p-3 shadow hover:shadow-lg transition-all duration-200 text-slate-900"
                    style="background-color: {{ $bgColorVar }}; opacity: {{ $opacity }}">
                    <div class="flex items-start justify-between gap-3">
                        {{-- Icona e contenuto --}}
                        <div class="flex items-start gap-2 flex-1 min-w-0">
                            {{-- Icona --}}
                            <div class="shrink-0">
                                <div class="w-8 h-8 rounded-full bg-slate-900/20 flex items-center justify-center">
                                    @svg($icon, 'w-4 h-4 text-slate-900')
                                </div>
                            </div>

                            {{-- Contenuto --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2">
                                    <h3 class="text-xs font-semibold truncate ">
                                        {{ $data['title'] ?? 'Notifica' }}
                                    </h3>
                                    @if (!$notification->read_at)
                                        <span
                                            class="text-xs px-1.5 py-0.5 rounded bg-white/60  font-medium">Nuovo</span>
                                    @endif
                                </div>

                                @if (isset($data['body']))
                                    <p class="mt-0.5 text-xs text-slate-900/90 line-clamp-2">
                                        {{ $data['body'] }}
                                    </p>
                                @endif

                                <p class="mt-1 text-xs text-slate-900 flex items-center gap-1">
                                    @svg('tabler-clock', 'w-3 h-3')
                                    <span class="truncate">{{ $notification->created_at->diffForHumans() }}</span>
                                    @if ($notification->read_at)
                                        <span class="text-slate-900 font-bold">· Letta</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Azioni --}}
                        <div class="flex items-center gap-4 justify-center shrink-0">
                            @if (!$notification->read_at)
                                <x-filament::icon-button icon="tabler-check" size="sm"
                                    wire:click="markAsRead('{{ $notification->id }}')" tooltip="Segna come letta"
                                    color="white" class="bg-white/60! hover:bg-white/90!" />
                            @endif

                            <x-filament::icon-button icon="tabler-trash" size="sm"
                                wire:click="deleteNotification('{{ $notification->id }}')" tooltip="Elimina"
                                color="white" class="bg-white/60! hover:bg-white/90!" />
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        @svg('tabler-bell-off', 'w-8 h-8 text-gray-400')
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-900 mb-1">
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
