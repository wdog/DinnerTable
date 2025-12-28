{{--
    Componente Toggle Vista Calendario

    Pulsanti per switchare tra vista mensile e settimanale del calendario.

    Props:
    - viewType: string - Tipo di vista corrente ('month' o 'week')
--}}

@props(['viewType'])

<div class="flex items-center justify-center gap-2">

    {{-- Pulsante Vista Mensile --}}
    <button wire:click="changeViewType('month')" type="button"
        class="group relative w-48 overflow-hidden rounded-lg border border-lime-400  shadow bg-lime-100 ">
        <div
            class="absolute inset-0 w-3 bg-lime-400 transition-all duration-250 ease-out group-hover:w-full {{ $viewType === 'month' ? 'w-full' : '' }}">
        </div>

        <div class="flex gap-2 px-4 py-2 items-center justify-center">

            <svg class="w-5 h-5 relative text-lime-400   group-hover:text-slate-700  {{ $viewType === 'month' ? 'text-slate-700' : '' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span
                class="relative  text-lime-400 group-hover:text-slate-700   {{ $viewType === 'month' ? 'text-slate-700' : '' }}">Calendario
                Mensile</span>
        </div>
    </button>

    {{-- Pulsante Vista Settimanale --}}
    <button wire:click="changeViewType('week')" type="button"
        class="group relative w-48 overflow-hidden rounded-lg border border-amber-400   shadow bg-amber-100">
        <div
            class="absolute inset-0 w-3 bg-amber-400  transition-all duration-250 ease-out group-hover:w-full {{ $viewType === 'week' ? 'w-full' : '' }}">
        </div>

        <div class="flex gap-2 px-4 py-2 items-center justify-center">

            <svg class="w-5 h-5 text-amber-300  relative group-hover:text-slate-700  {{ $viewType === 'week' ? 'text-slate-700' : '' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span
                class="relative text-amber-300  group-hover:text-slate-700  {{ $viewType === 'week' ? 'text-slate-700' : '' }}">Vista
                Settimanale</span>
        </div>
    </button>
</div>
