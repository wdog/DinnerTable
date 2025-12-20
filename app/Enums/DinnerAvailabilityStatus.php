<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DinnerAvailabilityStatus: string implements HasColor, HasIcon, HasLabel
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case MAYBE = 'maybe';

    public function getLabel(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponibile',
            self::UNAVAILABLE => 'Non disponibile',
            self::MAYBE => 'Forse',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::UNAVAILABLE => 'danger',
            self::MAYBE => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::AVAILABLE => 'tabler-circle-check',
            self::UNAVAILABLE => 'tabler-circle-x',
            self::MAYBE => 'tabler-brain',
        };
    }
}
