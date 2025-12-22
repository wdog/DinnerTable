<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DinnerAvailabilityStatus: string implements HasColor, HasIcon, HasLabel
{
    // Stati per HOST (can_host = true)
    case AVAILABLE_TO_HOST = 'available_to_host';
    case ALMOST_FULL       = 'almost_full';
    case FULL              = 'full';
    case HOST_CANCELLED    = 'host_cancelled';
    case COMPLETED         = 'completed';

    // Stati per GUEST (can_host = false)
    case AVAILABLE = 'available';

    public function getLabel(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'Disponibile ad ospitare',
            self::ALMOST_FULL       => 'Quasi pieno',
            self::FULL              => 'Pieno',
            self::HOST_CANCELLED    => 'Annullato',
            self::COMPLETED         => 'Completato',
            // Guest state
            self::AVAILABLE => 'Disponibile',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'success',
            self::ALMOST_FULL       => 'warning',
            self::FULL              => 'danger',
            self::HOST_CANCELLED    => 'danger',
            self::COMPLETED         => 'info',
            // Guest state
            self::AVAILABLE => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            // Host states
            self::AVAILABLE_TO_HOST => 'tabler-chef-hat-filled',
            self::ALMOST_FULL       => 'tabler-users',
            self::FULL              => 'tabler-door-off',
            self::HOST_CANCELLED    => 'tabler-ban',
            self::COMPLETED         => 'tabler-thumb-up',
            // Guest state
            self::AVAILABLE => 'tabler-tools-kitchen-3',
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
            self::COMPLETED,
        ]);
    }

    /**
     * Verifica se lo stato è valido per un guest.
     */
    public function isGuestStatus(): bool
    {
        return $this === self::AVAILABLE;
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

    public function canUpdateBookings(): bool
    {
        return ! in_array($this, [
            self::AVAILABLE_TO_HOST,
            self::COMPLETED,
            self::HOST_CANCELLED,
        ]);
    }
}
