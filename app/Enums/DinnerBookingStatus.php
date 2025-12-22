<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum per gli stati delle prenotazioni.
 *
 * Stati disponibili:
 * - PENDING: Prenotazione in attesa di conferma dall'host
 * - CONFIRMED: Prenotazione confermata dall'host
 * - CANCELLED: Prenotazione cancellata (dall'host o dal guest)
 * - COMPLETED: Prenotazione completata (cena terminata)
 */
enum DinnerBookingStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING   => 'In attesa',
            self::CONFIRMED => 'Confermato',
            self::CANCELLED => 'Cancellato',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING   => 'warning',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING   => 'tabler-clock',
            self::CONFIRMED => 'tabler-check',
            self::CANCELLED => 'tabler-x',
        };
    }
}
