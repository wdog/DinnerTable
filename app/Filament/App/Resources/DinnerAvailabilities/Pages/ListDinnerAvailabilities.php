<?php

namespace App\Filament\App\Resources\DinnerAvailabilities\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\App\Resources\DinnerAvailabilities\DinnerAvailabilityResource;

class ListDinnerAvailabilities extends ListRecords
{
    protected static string $resource = DinnerAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            CreateAction::make()
                ->icon('tabler-chef-hat')
                ->label('Crea nuovo evento')
                ->visible( ! empty($user->dinnerGroup)),
        ];
    }
}
