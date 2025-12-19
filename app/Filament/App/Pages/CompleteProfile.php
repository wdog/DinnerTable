<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class CompleteProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.app.pages.complete-profile';

    public ?array $data = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Se il profilo è già completo, reindirizza alla dashboard
        if ($user->hasCompletedProfile()) {
            $this->redirect(route('filament.dinner.pages.dashboard'));

            return;
        }

        $this->form->fill([
            'city' => $user->profile->city,
            'address' => $user->profile->address,
            'house_number' => $user->profile->house_number,
            'postal_code' => $user->profile->postal_code,
            'max_guests' => $user->profile->max_guests,
            'privacy_accepted' => ! is_null($user->profile->privacy_accepted_at),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Informazioni Privacy')
                ->description('Accetta la privacy policy per continuare')
                ->schema([
                    Forms\Components\Checkbox::make('privacy_accepted')
                        ->label('Accetto la privacy policy')
                        ->required()
                        ->accepted(),
                ]),

            Section::make('Indirizzo e Ospitalità')
                ->description('Inserisci il tuo indirizzo e il numero massimo di ospiti che puoi accogliere')
                ->schema([
                    Forms\Components\TextInput::make('address')
                        ->label('Via')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('house_number')
                        ->label('Civico')
                        ->required()
                        ->maxLength(10),

                    Forms\Components\TextInput::make('postal_code')
                        ->label('Cap')
                        ->required()
                        ->maxLength(5),

                    Forms\Components\TextInput::make('city')
                        ->label('Città')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('max_guests')
                        ->label('Numero massimo di ospiti')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(4),
                ])
                ->columns(2),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            /** @var User $user */
            $user = Auth::user();
            $user->profile->update([
                'city' => $data['city'],
                'address' => $data['address'],
                'house_number' => $data['house_number'],
                'postal_code' => $data['postal_code'],
                'max_guests' => $data['max_guests'],
                'privacy_accepted_at' => $data['privacy_accepted'] ? now() : null,
            ]);

            Notification::make()
                ->success()
                ->title('Profilo completato!')
                ->body('Il tuo profilo è stato completato con successo.')
                ->send();

            $this->redirect(route('filament.dinner.pages.dashboard'));
        } catch (Halt $exception) {
            return;
        }
    }
}
