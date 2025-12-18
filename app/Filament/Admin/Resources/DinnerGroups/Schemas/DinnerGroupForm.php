<?php

namespace App\Filament\Admin\Resources\DinnerGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DinnerGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('team_code')
                    ->required(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
            ]);
    }
}
