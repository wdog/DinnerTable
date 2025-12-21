<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;

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
