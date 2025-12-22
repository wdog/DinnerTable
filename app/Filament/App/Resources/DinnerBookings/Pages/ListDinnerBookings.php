<?php

namespace App\Filament\App\Resources\DinnerBookings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\App\Resources\DinnerBookings\DinnerBookingResource;

class ListDinnerBookings extends ListRecords
{
    protected static string $resource = DinnerBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
