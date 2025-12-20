<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDinnerAvailability extends EditRecord
{
    protected static string $resource = DinnerAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
