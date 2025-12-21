<?php

namespace App\Filament\Admin\Resources\DinnerGroups\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\DinnerGroups\DinnerGroupResource;

class CreateDinnerGroup extends CreateRecord
{
    protected static string $resource = DinnerGroupResource::class;
}
