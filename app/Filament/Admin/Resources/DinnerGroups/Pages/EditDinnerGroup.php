<?php

namespace App\Filament\Admin\Resources\DinnerGroups\Pages;

use App\Filament\Admin\Resources\DinnerGroups\DinnerGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDinnerGroup extends EditRecord
{
    protected static string $resource = DinnerGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
