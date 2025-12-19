<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;

/**
 * Pagina per il completamento del profilo utente.
 *
 * Questa pagina obbliga gli utenti a completare le informazioni mancanti del profilo
 * prima di poter accedere all'applicazione. Raccoglie dati di indirizzo, ospitalità
 * e accettazione della privacy policy.
 */
class CompleteProfile extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * Icona di navigazione della pagina.
     *
     * @var string|BackedEnum|null
     */
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    /**
     * Indica se la pagina deve apparire nella navigazione.
     *
     * @var bool
     */
    protected static bool $shouldRegisterNavigation = false;

    /**
     * Nome della vista Blade da utilizzare.
     *
     * @var string
     */
    protected string $view = 'filament.app.pages.complete-profile';

    /**
     * Dati del form.
     *
     * @var array|null
     */
    public ?array $data = [];

    /**
     * Inizializza la pagina e carica i dati del profilo utente.
     *
     * Se il profilo è già completo, reindirizza l'utente alla dashboard.
     * Altrimenti, pre-compila il form con i dati esistenti del profilo.
     *
     * @return void
     */
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

    /**
     * Definisce il form per il completamento del profilo.
     *
     * Il form è suddiviso in due sezioni:
     * - Informazioni Privacy: accettazione della privacy policy
     * - Indirizzo e Ospitalità: dati di residenza e capacità di ospitare
     *
     * @param Schema $schema Schema del form
     * @return Schema Schema configurato con i campi del form
     */
    public function form(Schema $schema)
    {
        return $schema
            ->schema([
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
            ])
            ->statePath('data');
    }

    /**
     * Salva i dati del profilo completato.
     *
     * Aggiorna il profilo dell'utente con i dati inseriti nel form,
     * mostra una notifica di successo e reindirizza alla dashboard.
     *
     * @return void
     * @throws Halt Se il salvataggio viene interrotto
     */
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
