<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DinnerAvailabilityStatus: string implements HasColor, HasIcon, HasLabel
{
    // Stati per HOST (can_host = true)
    case AVAILABLE_TO_HOST = 'available_to_host';
    case ALMOST_FULL = 'almost_full';
    case FULL = 'full';
    case HOST_CANCELLED = 'host_cancelled';

    // Stati per GUEST (can_host = false)
    case AVAILABLE = 'available';
    case BOOKED = 'booked';
    case UNAVAILABLE = 'unavailable';

    public function getLabel(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'Cucina',
            self::ALMOST_FULL => 'Quasi pieno',
            self::FULL => 'Pieno',
            self::HOST_CANCELLED => 'Annullato',
            // Guest states
            self::AVAILABLE => 'Mangia',
            self::BOOKED => 'Ha Prenotato',
            self::UNAVAILABLE => 'Non è disponibile',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'success',
            self::ALMOST_FULL => 'warning',
            self::FULL => 'danger',
            self::HOST_CANCELLED => 'danger',
            // Guest states
            self::AVAILABLE => 'purple',
            self::BOOKED => 'purple',
            self::UNAVAILABLE => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'tabler-chef-hat-filled',
            self::ALMOST_FULL => 'tabler-users',
            self::FULL => 'tabler-door-off',
            self::HOST_CANCELLED => 'tabler-ban',
            // Guest states
            self::AVAILABLE => 'tabler-tools-kitchen-3',
            self::BOOKED => 'tabler-tools-kitchen-3',
            self::UNAVAILABLE => 'tabler-circle-x',
        };
    }

    /**
     * Verifica se lo stato è valido per un host.
     */
    public function isHostStatus(): bool
    {
        return in_array($this, [
            self::AVAILABLE_TO_HOST,
            self::ALMOST_FULL,
            self::FULL,
            self::HOST_CANCELLED,
        ]);
    }

    /**
     * Verifica se lo stato è valido per un guest.
     */
    public function isGuestStatus(): bool
    {
        return in_array($this, [
            self::AVAILABLE,
            self::BOOKED,
            self::UNAVAILABLE,
        ]);
    }

    /**
     * Verifica se l'host può accettare prenotazioni in questo stato.
     */
    public function canAcceptBookings(): bool
    {
        return in_array($this, [
            self::AVAILABLE_TO_HOST,
            self::ALMOST_FULL,
        ]);
    }
}
