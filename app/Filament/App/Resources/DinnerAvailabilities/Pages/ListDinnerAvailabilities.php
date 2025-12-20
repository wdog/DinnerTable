<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDinnerAvailabilities extends ListRecords
{
    protected static string $resource = DinnerAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
