<?php

namespace App\Filament\Admin\Resources\DinnerGroups\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DinnerGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Gruppo')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([


                        TextInput::make('name')
                            ->required(),
                        TextInput::make('group_code')
                            ->required(),

                        FileUpload::make('group_image')
                            ->label('Dinner Group Image')
                            ->image()
                            ->previewable()
                            ->disk('public')
                            ->directory('dinner-group')
                            ->visibility('public')
                            ->avatar()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->helperText('Carica una foto del profilo (max 2MB)')
                    ]),
            ]);
    }
}
