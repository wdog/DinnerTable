<div class="space-y-4">
    {{-- Informazioni utente --}}
    <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        @if($user->avatar_url)
            <img src="{{ Storage::disk('public')->url($user->avatar_url) }}"
                 alt="{{ $user->nome }} {{ $user->cognome }}"
                 class="w-16 h-16 rounded-full object-cover">
        @else
            <img src="{{ url('/images/default-avatar.svg') }}"
                 alt="Default Avatar"
                 class="w-16 h-16 rounded-full">
        @endif

        <div>
            <h3 class="text-lg font-semibold">{{ $user->nome }} {{ $user->cognome }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->citta }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Max ospiti: {{ $user->max_ospiti ?? 'N/D' }}</p>
        </div>
    </div>

    {{-- Lista disponibilità --}}
    @if($availabilities->isEmpty())
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p>Nessuna disponibilità dichiarata per dicembre 2025</p>
        </div>
    @else
        <div class="space-y-2">
            <h4 class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                Disponibilità dichiarate ({{ $availabilities->count() }})
            </h4>

            <div class="space-y-2">
                @foreach($availabilities->sortBy('dinnerDate.dinner_date') as $availability)
                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center gap-3">
                            {{-- Icona status --}}
                            <span class="text-2xl">
                                @if($availability->status->value === 'available')
                                    ✓
                                @elseif($availability->status->value === 'maybe')
                                    ?
                                @elseif($availability->status->value === 'unavailable')
                                    ✗
                                @elseif($availability->status->value === 'cancelled')
                                    ⊘
                                @elseif($availability->status->value === 'booked')
                                    ●
                                @endif
                            </span>

                            {{-- Data --}}
                            <div>
                                <p class="font-medium">
                                    {{ \Carbon\Carbon::parse($availability->dinnerDate->dinner_date)->isoFormat('dddd D MMMM YYYY') }}
                                </p>

                                {{-- Status badge --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($availability->status->value === 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($availability->status->value === 'maybe') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($availability->status->value === 'unavailable') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($availability->status->value === 'cancelled') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @elseif($availability->status->value === 'booked') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @endif">
                                    {{ $availability->status->getLabel() }}
                                </span>

                                {{-- Può ospitare --}}
                                @if($availability->can_host)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 ml-1">
                                        🏠 Può ospitare
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Note --}}
                        @if($availability->note)
                            <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                {{ $availability->note }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Statistiche riepilogative --}}
        <div class="grid grid-cols-2 gap-4 mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Totale disponibilità</p>
                <p class="text-2xl font-bold">{{ $availabilities->count() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Può ospitare</p>
                <p class="text-2xl font-bold">{{ $availabilities->where('can_host', true)->count() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Disponibile</p>
                <p class="text-2xl font-bold text-green-600">{{ $availabilities->where('status.value', 'available')->count() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-400">Forse</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $availabilities->where('status.value', 'maybe')->count() }}</p>
            </div>
        </div>
    @endif
</div>
