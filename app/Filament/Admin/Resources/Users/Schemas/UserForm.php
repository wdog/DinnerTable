<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ]);
    }
}
