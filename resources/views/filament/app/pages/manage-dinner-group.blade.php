<x-filament-panels::page>
    @php
        $user = auth()->user();
        $group = $user->dinnerGroup;
    @endphp

    @if ($group)
        {{-- Utente è già in un gruppo --}}
        <div class="space-y-6">
            {{-- Card Informazioni Gruppo --}}
            <x-filament::section>
                <x-slot name="heading">
                    Informazioni Gruppo
                </x-slot>

                <x-slot name="description">
                    Dettagli del tuo gruppo cena
                </x-slot>

                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome Gruppo</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $group->name }}</dd>
                    </div>

                    @if ($group->slogan)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slogan</dt>
                            <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ $group->slogan }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Codice Gruppo</dt>
                        <dd class="mt-1">
                            <div
                                class="inline-flex items-center gap-2 rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-2">
                                <span
                                    class="text-2xl font-bold font-mono text-primary-600 dark:text-primary-400">{{ $group->group_code }}</span>
                                <x-filament::icon-button icon="heroicon-o-clipboard" size="sm"
                                    onclick="navigator.clipboard.writeText('{{ $group->group_code }}'); $tooltip('Copiato!', { theme: 'success' })"
                                    tooltip="Copia codice" />
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Condividi questo codice con i tuoi amici per farli unire al gruppo
                            </p>
                        </dd>
                    </div>

                    <div class="flex space-x-4 items-center">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato da</dt>
                            <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ $group->creator->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato il</dt>
                            <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                {{ $group->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Card Membri del Gruppo --}}
            <x-filament::section>
                <x-slot name="heading">
                    Membri del Gruppo
                </x-slot>

                <x-slot name="description">
                    {{ $group->members->count() }} {{ $group->members->count() === 1 ? 'membro' : 'membri' }}
                </x-slot>

                <div class="">
                    <div class="flex items-center justify-start gap-2 p-3">
                        @foreach ($group->members as $member)
                            <div
                                class="flex items-start  gap-3 border p-2 rounded-md border-gray-700 dark:bg-slate-800 bg-gray-300">

                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $member->name }}
                                        @if ($member->id === $group->created_by)
                                            <x-filament::badge color="success"
                                                class='px-2'>Creatore</x-filament::badge>
                                        @endif
                                        @if ($member->id === $user->id)
                                            <x-filament::badge color="info" class='px-2'>Tu</x-filament::badge>
                                        @endif
                                    </p>

                                </div>
                                @if ($member->profile)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Max ospiti</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $member->profile->max_guests }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-filament::section>
        </div>
    @else
        {{-- Utente non è in un gruppo --}}
        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Benvenuto!
                </x-slot>

                <x-slot name="description">
                    Non fai ancora parte di nessun gruppo cena
                </x-slot>

                <div class="prose dark:prose-invert max-w-none">
                    <p>Per iniziare a organizzare cene con i tuoi amici, devi:</p>
                    <ul>
                        <li><strong>Creare un nuovo gruppo</strong> - Diventerai il creatore e riceverai un codice da
                            condividere</li>
                        <li><strong>Unirti a un gruppo esistente</strong> - Inserisci il codice gruppo che ti è stato
                            condiviso</li>
                    </ul>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Usa i pulsanti in alto a destra per procedere.
                    </p>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
