<?php

namespace App\Filament\App\Auth\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    /**
     * Imposta la larghezza massima del form.
     */
    public function getMaxWidth(): Width|string|null
    {
        return Width::FiveExtraLarge;
    }

    /**
     * Definisce lo schema del form per la modifica del profilo.
     * Include sezioni per: informazioni personali, sicurezza, indirizzo, ospitalità e privacy.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make('Informazioni Personali')
                        // ->relationship('profile')
                        ->schema([
                            FileUpload::make('avatar_url')
                                ->label('Avatar')
                                ->directory('avatars')
                                ->disk('public')
                                ->visibility('public')
                                ->avatar()
                                ->circleCropper()
                                ->maxSize(2048)
                                ->helperText('Carica una foto del profilo (max 2MB)')
                                ->columnSpanFull(),

                        ])
                        ->columns(2),
                    Section::make('Sicurezza')
                        ->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                            $this->getCurrentPasswordFormComponent(),
                        ]),

                ]),
                Section::make('Indirizzo')
                    ->relationship('profile')
                    ->schema([
                        TextInput::make('address')
                            ->label('Via')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('house_number')
                            ->label('Civico')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('postal_code')
                            ->label('CAP')
                            ->required()
                            ->maxLength(5)
                            ->numeric(),

                        TextInput::make('city')
                            ->label('Città')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Flex::make([
                    Section::make('Ospitalità')
                        ->relationship('profile')
                        ->schema([
                            TextInput::make('max_guests')
                                ->label('Numero massimo di ospiti')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(50),
                        ]),

                    Section::make('Privacy')
                        ->schema([
                            Checkbox::make('privacy_accepted')
                                ->label('Accetto la privacy policy')
                                ->required()
                                ->accepted(),
                        ]),

                ])->from('md'),
            ]);
    }

    /**
     * Prepara i dati prima di caricarli nel form.
     * Converte il campo privacy_accepted_at (timestamp) in privacy_accepted (boolean).
     *
     * @param  array  $data  Dati dell'utente
     * @return array Dati preparati per il form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Converti privacy_accepted_at (timestamp) in privacy_accepted (boolean)
        $data['privacy_accepted'] = ! is_null($this->getUser()->profile->privacy_accepted_at);

        // dd($data);
        return $data;
    }

    /**
     * Prepara i dati prima di salvarli nel database.
     * Converte il campo privacy_accepted (boolean) in privacy_accepted_at (timestamp).
     * Se privacy_accepted è true, imposta privacy_accepted_at a now(), altrimenti a null.
     *
     * @param  array  $data  Dati dal form
     * @return array Dati preparati per il salvataggio
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $profileData = [];

        // Converti privacy_accepted (boolean) in privacy_accepted_at (timestamp)
        if (isset($data['privacy_accepted'])) {
            $profileData['privacy_accepted_at'] = $data['privacy_accepted'] ? now() : null;
        }

        if (isset($data['avatar_url'])) {
            $profileData['avatar_url'] = $data['avatar_url'];
            unset($data['avatar_url']);
        }

        // Aggiorna il profilo dell'utente
        $this->getUser()->profile->update($profileData);

        return $data;
    }
}
