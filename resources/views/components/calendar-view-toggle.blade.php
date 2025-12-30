{{--
    Componente Toggle Vista Calendario

    Pulsanti per switchare tra vista mensile e settimanale del calendario.

    Props:
    - viewType: string - Tipo di vista corrente ('month' o 'week')
--}}

@props(['viewType'])

<div class="flex space-x-2 text-sm">

    {{-- Pulsante Vista Mensile --}}
    <button wire:click="changeViewType('month')" type="button"
        class='bg-lime-100
        text-lime-400 {{ $viewType === 'month' ? 'text-slate-700 bg-lime-400' : '' }} group  rounded-lg'>

        <div class="flex gap-2 px-4 py-2 items-center justify-center group-hover:text-slate-700 group-hover:bg-lime-400 group-hover:rounded-lg">
            @svg('tabler-calendar', 'w-5 h-5')
            <span>Vista Mensile</span>
        </div>
    </button>

    {{-- Pulsante Vista Settimanale --}}
    <button wire:click="changeViewType('week')" type="button"
        class='bg-amber-100
        text-amber-400 {{ $viewType === 'week' ? 'text-slate-700 bg-amber-400' : '' }} group rounded-lg'>


        <div class="flex gap-2 px-4 py-2 items-center justify-center group-hover:text-slate-700 group-hover:bg-amber-400 group-hover:rounded-lg">
            @svg('tabler-calendar', 'w-5 h-5')
            <span>Vista Settimanale</span>
        </div>
    </button>
</div>
