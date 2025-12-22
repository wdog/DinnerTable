<?php

namespace App\Filament\App\Resources\DinnerBookings\Pages;

use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Resources\DinnerBookings\DinnerBookingResource;

class CreateDinnerBooking extends CreateRecord
{
    protected static string $resource = DinnerBookingResource::class;

    /**
     * Modifica i dati prima di creare il record.
     * Aggiunge automaticamente l'ID dell'utente autenticato come guest.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guest_user_id'] = Auth::id();

        return $data;
    }
}
