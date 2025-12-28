@props(['target', 'direction' => 'down'])

<div class="fixed right-0 bottom-2 justify-center items-end inset-0 flex mt-2 pointer-events-none z-20">
    <a href="#{{ $target }}"
        class="pointer-events-auto   group inline-flex
        items-center justify-center w-12 h-12  text-lime-700
        rounded-full  animate-pulse bg-lime-300  duration-500"
        aria-label="Vai alla sezione {{ $target }}">

        @if ($direction === 'down')
            <!-- Chevron Down -->
            @svg('tabler-arrow-narrow-down-dashed', 'w-6 h-6')
        @endif

        @if ($direction === 'up')
            <!-- Chevron Up -->
            @svg('tabler-arrow-narrow-up-dashed', 'w-6 h-6')
        @endif
    </a>
</div>
