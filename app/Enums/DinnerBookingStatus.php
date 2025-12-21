<?php

namespace App\Enums;

enum DinnerBookingStatus: string
{
    case PENDING = 'In attesa di conferma';
    case CONFIRMED = 'Confermato';
    case CANCELLED = 'Cancellato';
}
