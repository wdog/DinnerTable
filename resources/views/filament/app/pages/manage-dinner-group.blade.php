<x-filament-panels::page>
    @php
        $group = $this->getUserGroup();
    @endphp

    <x-filament-actions::modals />

    <div class="space-y-6">
        @if ($group)
            {{-- Informazioni Gruppo --}}
            <x-filament::section heading="Informazioni Gruppo" description="Dettagli del tuo gruppo cena"
                icon="tabler-info-circle">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Nome Gruppo --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="tabler-chef-hat" class="h-5 w-5 text-primary-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome Gruppo</span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $group->name }}</p>
                    </div>

                    @if ($group->slogan)
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <x-filament::icon icon="tabler-quote"
                                    class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Slogan</span>
                            </div>
                            <p class="text-base text-gray-900 dark:text-white">{{ $group->slogan }}</p>
                        </div>
                    @endif
                    {{-- Codice Gruppo --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="tabler-key" class="h-5 w-5 text-success-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Codice Gruppo</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-filament::badge color="success" class="text-2xl">
                                {{ $group->group_code }}
                            </x-filament::badge>
                            <x-filament::icon-button icon="tabler-copy"
                                x-on:click="
                                    navigator.clipboard.writeText('{{ $group->group_code }}');
                                    $tooltip('Codice copiato!', { theme: $store.theme })
                                " />
                        </div>
                    </div>
                </div>



                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    {{-- Creato da --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="tabler-crown" class="h-5 w-5 text-warning-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato da</span>
                        </div>
                        <p class="text-base text-gray-900 dark:text-white">{{ $group->creator->name }}</p>
                    </div>

                    {{-- Creato il --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="tabler-calendar-plus"
                                class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato il</span>
                        </div>
                        <p class="text-base text-gray-900 dark:text-white">{{ $group->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    {{-- Membri Totali --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-filament::icon icon="tabler-users-group" class="h-5 w-5 text-info-500" />
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Membri Totali</span>
                        </div>
                        <x-filament::badge color="info" size="lg">
                            {{ $group->members()->count() }}
                        </x-filament::badge>
                    </div>
                </div>
            </x-filament::section>

            {{-- Tabella Membri del Gruppo --}}
            <x-filament::section heading="Membri del Gruppo"
                description="Visualizza e gestisci i membri del tuo gruppo cena"
                icon="tabler-users">
                {{ $this->table }}
            </x-filament::section>
        @else
            {{-- Benvenuto - Nessun Gruppo --}}
            <x-filament::section heading="Benvenuto!" description="Non fai ancora parte di nessun gruppo cena"
                icon="tabler-user-plus">

                <div class="prose dark:prose-invert max-w-none">
                    <p>Per iniziare a organizzare cene con i tuoi amici, scegli una delle opzioni qui sotto:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    {{-- Card Crea Gruppo --}}
                    <div wire:click="mountAction('createGroupAction')"
                        class="group relative flex flex-col items-center text-center p-8 rounded-xl border-2 border-lime-300 dark:border-lime-700 bg-lime-50 dark:bg-lime-900 shadow-sm cursor-pointer transition-all duration-300 hover:shadow-lg hover:shadow-lime-500/30 dark:hover:shadow-lime-500/20 hover:-translate-y-1 hover:border-lime-500 dark:hover:border-lime-500">

                        {{-- Gradient overlay on hover --}}
                        <div
                            class="absolute inset-0 rounded-xl bg-linear-to-br from-lime-100 to-lime-200/50 dark:from-lime-800/50 dark:to-lime-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <div class="relative z-10">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-lime-200 dark:bg-lime-800 mb-4 group-hover:scale-110 transition-transform duration-300 group-hover:bg-lime-300 dark:group-hover:bg-lime-700">
                                <x-filament::icon icon="tabler-chef-hat-filled"
                                    class="h-10 w-10 text-amber-700 dark:text-amber-300" />
                            </div>
                            <h3 class="text-xl font-bold text-lime-900 dark:text-lime-100 mb-3">Crea Nuovo Gruppo</h3>
                            <p class="text-sm text-lime-700 dark:text-lime-300 leading-relaxed">
                                Diventerai il creatore del gruppo e riceverai un codice unico da condividere con i tuoi
                                amici.
                            </p>
                        </div>
                    </div>

                    {{-- Card Unisciti Gruppo --}}
                    <div wire:click="openJoinGroupModal"
                        class="group relative flex flex-col items-center text-center p-8 rounded-xl border-2 border-cyan-300 dark:border-cyan-700 bg-cyan-50 dark:bg-cyan-900 shadow-sm cursor-pointer transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/30 dark:hover:shadow-cyan-500/20 hover:-translate-y-1 hover:border-cyan-500 dark:hover:border-cyan-500">

                        {{-- Gradient overlay on hover --}}
                        <div
                            class="absolute inset-0 rounded-xl bg-linear-to-br from-cyan-100 to-cyan-200/50 dark:from-cyan-800/50 dark:to-cyan-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <div class="relative z-10">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-cyan-200 dark:bg-cyan-800 mb-4 group-hover:scale-110 transition-transform duration-300 group-hover:bg-cyan-300 dark:group-hover:bg-cyan-700">
                                <x-filament::icon icon="tabler-basket-filled"
                                    class="h-10 w-10 text-amber-700 dark:text-amber-300" />
                            </div>
                            <h3 class="text-xl font-bold text-cyan-900 dark:text-cyan-100 mb-3">Unisciti a un Gruppo</h3>
                            <p class="text-sm text-cyan-700 dark:text-cyan-300 leading-relaxed">
                                Inserisci il codice gruppo che ti è stato condiviso per unirti a un gruppo esistente.
                            </p>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
