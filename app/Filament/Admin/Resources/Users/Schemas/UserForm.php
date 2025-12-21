<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informazioni Utente')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->dehydrated(fn ($state) => filled($state))
                            ->requiredWith('name'),
                        Toggle::make('is_admin')
                            ->label('Amministratore')
                            ->default(false)
                            ->inline(false),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email verificata il'),
                    ]),
            ]);
    }
}
