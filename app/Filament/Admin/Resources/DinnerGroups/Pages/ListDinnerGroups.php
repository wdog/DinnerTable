<?php

namespace App\Filament\Admin\Resources\DinnerGroups\Pages;

use App\Filament\Admin\Resources\DinnerGroups\DinnerGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDinnerGroups extends ListRecords
{
    protected static string $resource = DinnerGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
