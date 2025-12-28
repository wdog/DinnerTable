{{--
    Componente Toggle Vista Calendario

    Pulsanti per switchare tra vista mensile e settimanale del calendario.

    Props:
    - viewType: string - Tipo di vista corrente ('month' o 'week')
--}}

@props(['viewType'])

<div class="flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
    {{-- Pulsante Vista Mensile --}}
    <button wire:click="changeViewType('month')" type="button" @class([
        'flex items-center bg-white gap-2 px-4 py-2 rounded-md transition-all font-semibold shadow hover:shadow-xl',
        'text-lime-500 !bg-lime-100' => $viewType === 'month',
        'text-orange-500' => $viewType !== 'month',
    ])>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>Calendario Mensile</span>
    </button>

    {{-- Pulsante Vista Settimanale --}}
    <button wire:click="changeViewType('week')" type="button" @class([
        'flex items-center bg-white gap-2 px-4 py-2 rounded-md transition-all font-semibold shadow hover:shadow-xl',
        'text-orange-500 !bg-orange-100' => $viewType === 'week',
        'text-lime-500' => $viewType !== 'week',
    ])>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <span>Vista Settimanale</span>
    </button>
</div>
