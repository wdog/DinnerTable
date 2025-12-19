<x-filament-panels::page>
    @php
        $group = $this->getUserGroup();
    @endphp

    <x-filament-actions::modals />

    <div class="space-y-6">
        @if ($group)
            {{-- Informazioni Gruppo --}}
            <x-filament::section
                heading="Informazioni Gruppo"
                description="Dettagli del tuo gruppo cena"
                icon="heroicon-o-information-circle">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nome Gruppo --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="tabler-chef-hat" class="h-5 w-5 text-primary-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome Gruppo</span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $group->name }}</p>
                    </div>

                    {{-- Codice Gruppo --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-key" class="h-5 w-5 text-success-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Codice Gruppo</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-filament::badge color="success" size="lg">
                                {{ $group->group_code }}
                            </x-filament::badge>
                            <x-filament::icon-button
                                icon="heroicon-o-clipboard"
                                size="sm"
                                x-on:click="
                                    navigator.clipboard.writeText('{{ $group->group_code }}');
                                    $tooltip('Codice copiato!', { theme: $store.theme })
                                "
                            />
                        </div>
                    </div>
                </div>

                @if($group->slogan)
                    <div class="mt-4">
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-chat-bubble-left-ellipsis" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Slogan</span>
                        </div>
                        <p class="text-base text-gray-900 dark:text-white">{{ $group->slogan }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    {{-- Creato da --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-user" class="h-5 w-5 text-warning-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato da</span>
                        </div>
                        <p class="text-base text-gray-900 dark:text-white">{{ $group->creator->name }}</p>
                    </div>

                    {{-- Creato il --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-calendar" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato il</span>
                        </div>
                        <p class="text-base text-gray-900 dark:text-white">{{ $group->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    {{-- Membri Totali --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="heroicon-o-users" class="h-5 w-5 text-info-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Membri Totali</span>
                        </div>
                        <x-filament::badge color="info" size="lg">
                            {{ $group->members()->count() }}
                        </x-filament::badge>
                    </div>
                </div>
            </x-filament::section>

            {{-- Tabella Membri del Gruppo --}}
            <x-filament::section
                heading="Membri del Gruppo"
                description="Visualizza e gestisci i membri del tuo gruppo cena">
                {{ $this->table }}
            </x-filament::section>
        @else
            {{-- Benvenuto - Nessun Gruppo --}}
            <x-filament::section
                heading="Benvenuto!"
                description="Non fai ancora parte di nessun gruppo cena"
                icon="heroicon-o-user-group">

                <div class="prose dark:prose-invert max-w-none">
                    <p>Per iniziare a organizzare cene con i tuoi amici, scegli una delle opzioni qui sotto:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    {{-- Card Crea Gruppo --}}
                    <div
                        wire:click="mountAction('createGroupAction')"
                        class="group relative flex flex-col items-center text-center p-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm cursor-pointer transition-all duration-300 hover:shadow-lg hover:shadow-success-500/20 dark:hover:shadow-success-500/10 hover:-translate-y-1 hover:border-success-400 dark:hover:border-success-600">

                        {{-- Gradient overlay on hover --}}
                        <div class="absolute inset-0 rounded-xl bg-linear-to-br from-success-50/50 to-transparent dark:from-success-950/20 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-success-100 dark:bg-success-950/40 mb-4 group-hover:scale-110 transition-transform duration-300">
                                <x-filament::icon icon="tabler-chef-hat-filled" class="h-10 w-10 text-success-600 dark:text-success-400" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Crea Nuovo Gruppo</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Diventerai il creatore del gruppo e riceverai un codice unico da condividere con i tuoi amici.
                            </p>
                        </div>
                    </div>

                    {{-- Card Unisciti Gruppo --}}
                    <div
                        wire:click="openJoinGroupModal"
                        class="group relative flex flex-col items-center text-center p-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm cursor-pointer transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/20 dark:hover:shadow-primary-500/10 hover:-translate-y-1 hover:border-primary-400 dark:hover:border-primary-600">

                        {{-- Gradient overlay on hover --}}
                        <div class="absolute inset-0 rounded-xl bg-linear-to-br from-primary-50/50 to-transparent dark:from-primary-950/20 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 dark:bg-primary-950/40 mb-4 group-hover:scale-110 transition-transform duration-300">
                                <x-filament::icon icon="tabler-glass-full-filled" class="h-10 w-10 text-primary-600 dark:text-primary-400" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Unisciti a un Gruppo</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Inserisci il codice gruppo che ti è stato condiviso per unirti a un gruppo esistente.
                            </p>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
