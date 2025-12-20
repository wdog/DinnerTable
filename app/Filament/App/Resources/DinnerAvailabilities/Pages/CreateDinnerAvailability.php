<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;
use App\Models\DinnerDate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDinnerAvailability extends CreateRecord
{
    protected static string $resource = DinnerAvailabilityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $dinnerDate = DinnerDate::firstOrCreate([
            'dinner_date' => $data['dinnerDate']['dinner_date'],
            'dinner_group_id' => Auth::user()->dinner_group_id,
        ]);

        $data['dinner_date_id'] = $dinnerDate->id;
        $data['user_id'] = Auth::user()->id;

        return $data;
    }
}
