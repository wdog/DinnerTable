<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * Componente rating con stelle per Filament.
 *
 * Estende Field per creare un input di valutazione con stelle interattive (0-5).
 */
class RatingStar extends Field
{
    protected string $view = 'forms.components.rating-star';

    protected int $maxStars = 5;

    public function maxStars(int $stars): static
    {
        $this->maxStars = $stars;

        return $this;
    }

    public function getMaxStars(): int
    {
        return $this->maxStars;
    }
}
