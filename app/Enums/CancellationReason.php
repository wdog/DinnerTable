<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Enum per i motivi di cancellazione della cena da parte dell'host.
 *
 * Questo enum viene utilizzato quando un host annulla la propria
 * disponibilità e deve specificare il motivo della cancellazione.
 */
enum CancellationReason: string implements HasLabel
{
    case PERSONAL_EMERGENCY = 'personal_emergency';
    case ILLNESS            = 'illness';
    case WORK_COMMITMENT    = 'work_commitment';
    case FAMILY_REASON      = 'family_reason';
    case HOUSE_ISSUE        = 'house_issue';
    case OTHER              = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::PERSONAL_EMERGENCY => 'Emergenza personale',
            self::ILLNESS            => 'Malattia',
            self::WORK_COMMITMENT    => 'Impegno di lavoro',
            self::FAMILY_REASON      => 'Motivo familiare',
            self::HOUSE_ISSUE        => 'Problema domestico',
            self::OTHER              => 'Altro',
        };
    }
}
