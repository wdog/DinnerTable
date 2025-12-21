<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;

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
