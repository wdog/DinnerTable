<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $id = $getId();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
        $maxStars = $getMaxStars();
    @endphp

    <div class="ratings">
        <div class="stars">
            @for ($i = $maxStars; $i >= 1; $i--)
                @php
                    $uniqueId = $id . '-' . $i;
                @endphp
                <input @disabled($isDisabled) id="{{ $uniqueId }}" name="{{ $id }}" type="radio"
                    value="{{ $i }}" wire:loading.attr="disabled"
                    {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}" class="star-{{ $i }}" />
                <label class="star-{{ $i }}" for="{{ $uniqueId }}">
                    @svg('tabler-star-filled', 'w-8 h-8')
                </label>
            @endfor
            <span></span>
        </div>
    </div>
</x-dynamic-component>

<style>
    .ratings {
        position: relative;
        z-index: 1;
    }

    .ratings .stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 0.25rem;
    }

    .ratings .stars input[type=radio] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ratings .stars label {
        cursor: pointer;
        transition: all 0.2s;
        color: rgb(209 213 219);
        /* gray-300 */
    }

    .ratings .stars label:hover,
    .ratings .stars label:hover~label {
        color: rgb(251 191 36);
        /* amber-400 */
    }

    .ratings .stars input[type=radio]:checked~label {
        color: rgb(251 191 36);
        /* amber-400 */
    }

    .ratings .stars input[type=radio]:disabled~label {
        cursor: not-allowed;
        opacity: 0.5;
    }

    /* Dark mode */
    .dark .ratings .stars label {
        color: rgb(75 85 99);
        /* gray-600 dark */
    }

    .dark .ratings .stars label:hover,
    .dark .ratings .stars label:hover~label,
    .dark .ratings .stars input[type=radio]:checked~label {
        color: rgb(251 191 36);
        /* amber-400 */
    }
</style>
