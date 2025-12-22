<?php

namespace App\Filament\App\Resources\DinnerBookings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\App\Resources\DinnerBookings\DinnerBookingResource;

class EditDinnerBooking extends EditRecord
{
    protected static string $resource = DinnerBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        if ( ! $this->record->hostAvailability->status->canUpdateBookings()) {
            return [];
        }

        return parent::getFormActions();
    }
}
