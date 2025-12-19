<div class="space-y-4">
    <div class="flex items-center gap-4">
        <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
        </div>

        <div class="flex gap-2">
            @if ($isCreator)
                <x-filament::badge color="warning" icon="heroicon-o-star">
                    Creatore
                </x-filament::badge>
            @endif

            @if ($isYou)
                <x-filament::badge color="info" icon="heroicon-o-user">
                    Tu
                </x-filament::badge>
            @endif
        </div>
    </div>

    @if ($user->profile)
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-2 mb-2">
                    <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Indirizzo</span>
                </div>
                <p class="text-base text-gray-900 dark:text-white">
                    {{ $user->profile->street_address ?? 'Non specificato' }}
                </p>
                @if ($user->profile->city)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $user->profile->city }}
                    </p>
                @endif
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-2 mb-2">
                    <x-filament::icon icon="heroicon-o-users" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Capacità Ospiti</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $user->profile->max_guests ?? 0 }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Numero massimo di ospiti
                </p>
            </div>
        </div>

        @if ($user->profile->dietary_restrictions || $user->profile->notes)
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
                @if ($user->profile->dietary_restrictions)
                    <div class="mb-3">
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Restrizioni Alimentari</span>
                        </div>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $user->profile->dietary_restrictions }}
                        </p>
                    </div>
                @endif

                @if ($user->profile->notes)
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Note</span>
                        </div>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $user->profile->notes }}
                        </p>
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <x-filament::icon icon="heroicon-o-exclamation-circle" class="h-12 w-12 text-gray-400 mx-auto mb-2" />
            <p class="text-gray-500 dark:text-gray-400">Profilo non ancora completato</p>
        </div>
    @endif

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>Membro dal {{ $user->created_at->format('d/m/Y') }}</span>
            @if ($user->email_verified_at)
                <x-filament::badge color="success" size="xs">
                    Email Verificata
                </x-filament::badge>
            @endif
        </div>
    </div>
</div>
