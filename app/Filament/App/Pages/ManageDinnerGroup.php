<?php

namespace App\Filament\App\Pages;

use App\Models\DinnerGroup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Pagina per la gestione dei gruppi cena.
 *
 * Permette agli utenti di creare un nuovo gruppo cena, unirsi a un gruppo
 * esistente tramite codice, o uscire dal proprio gruppo corrente.
 * Ogni utente può appartenere a un solo gruppo alla volta.
 */
class ManageDinnerGroup extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * Nome della vista Blade da utilizzare.
     *
     * @var string
     */
    protected string $view = 'filament.app.pages.manage-dinner-group';

    /**
     * Icona di navigazione della pagina.
     *
     * @var string|BackedEnum|null
     */
    protected static string|BackedEnum|null $navigationIcon = 'tabler-chef-hat';

    /**
     * Etichetta del link di navigazione.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Il Mio Gruppo Cena';

    /**
     * Titolo della pagina.
     *
     * @var string|null
     */
    protected static ?string $title = 'Gestione Gruppo';

    /**
     * Dati del form per la creazione di un gruppo.
     *
     * @var array|null
     */
    public ?array $createData = [];

    /**
     * Dati del form per unirsi a un gruppo.
     *
     * @var array|null
     */
    public ?array $joinData = [];

    /**
     * Inizializza il componente e carica i dati iniziali.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * Ottiene l'utente correntemente autenticato.
     *
     * @return \App\Models\User
     */
    protected function getUser()
    {
        return Auth::user();
    }

    /**
     * Ottiene il gruppo cena dell'utente corrente.
     *
     * @return DinnerGroup|null Il gruppo dell'utente o null se non appartiene a nessun gruppo
     */
    protected function getUserGroup(): ?DinnerGroup
    {
        return $this->getUser()->dinnerGroup;
    }

    /**
     * Definisce lo schema del form per creare un nuovo gruppo.
     *
     * Raccoglie il nome del gruppo e uno slogan opzionale.
     *
     * @return array Schema dei campi del form
     */
    protected function getCreateGroupFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome del Gruppo')
                ->required()
                ->maxLength(255)
                ->placeholder('Es: Gli Amici della Cena'),

            TextInput::make('slogan')
                ->label('Slogan (opzionale)')
                ->maxLength(255)
                ->placeholder('Es: Mangiare insieme è meglio!'),
        ];
    }

    /**
     * Definisce lo schema del form per unirsi a un gruppo esistente.
     *
     * Richiede l'inserimento di un codice gruppo valido di 14 caratteri.
     *
     * @return array Schema dei campi del form
     */
    protected function getJoinGroupFormSchema(): array
    {
        return [
            TextInput::make('group_code')
                ->label('Codice Gruppo')
                ->required()
                ->length(14)
                ->placeholder('XXXXXXXX')
                ->rule('exists:dinner_groups,group_code'),
        ];
    }

    /**
     * Definisce l'azione per creare un nuovo gruppo cena.
     *
     * Genera un codice gruppo univoco, crea il gruppo nel database
     * e assegna automaticamente l'utente come membro fondatore.
     *
     * @return Action Azione configurata per la creazione del gruppo
     */
    public function createGroupAction(): Action
    {
        return Action::make('createGroup')
            ->label('Crea Nuovo Gruppo')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->schema($this->getCreateGroupFormSchema())
            ->action(function (array $data): void {
                try {
                    /** @var \App\Models\User $user */
                    $user = $this->getUser();

                    // Verifica se l'utente è già in un gruppo
                    if ($user->dinnerGroup) {
                        Notification::make()
                            ->danger()
                            ->title('Errore')
                            ->body('Sei già membro di un gruppo. Devi prima uscire dal gruppo corrente.')
                            ->send();

                        return;
                    }

                    // Genera un codice gruppo univoco
                    do {
                        $groupCode = strtoupper(Str::random(14));
                    } while (DinnerGroup::where('group_code', $groupCode)->exists());

                    // Crea il gruppo
                    $group = DinnerGroup::create([
                        'name' => $data['name'],
                        'slogan' => $data['slogan'] ?? null,
                        'group_code' => $groupCode,
                        'created_by' => $user->id,
                    ]);

                    // Aggiungi l'utente al gruppo
                    $user->update(['dinner_group_id' => $group->id]);

                    Notification::make()
                        ->success()
                        ->title('Gruppo Creato!')
                        ->body("Il tuo gruppo è stato creato. Codice: {$groupCode}")
                        ->send();

                    // Refresh della pagina
                    $this->redirect(static::getUrl());
                } catch (Halt $exception) {
                    return;
                }
            });
    }

    /**
     * Definisce l'azione per unirsi a un gruppo esistente.
     *
     * Verifica che l'utente non sia già membro di un gruppo,
     * valida il codice inserito e aggiunge l'utente al gruppo.
     *
     * @return Action Azione configurata per l'adesione al gruppo
     */
    public function joinGroupAction(): Action
    {
        return Action::make('joinGroup')
            ->label('Unisciti a un Gruppo')
            ->icon('heroicon-o-arrow-right-circle')
            ->color('primary')
            ->schema($this->getJoinGroupFormSchema())
            ->action(function (array $data): void {
                try {
                    /** @var \App\Models\User $user */
                    $user = $this->getUser();

                    // Verifica se l'utente è già in un gruppo
                    if ($user->dinnerGroup) {
                        Notification::make()
                            ->danger()
                            ->title('Errore')
                            ->body('Sei già membro di un gruppo. Devi prima uscire dal gruppo corrente.')
                            ->send();

                        return;
                    }

                    // Trova il gruppo tramite codice
                    $group = DinnerGroup::where('group_code', strtoupper($data['group_code']))->first();

                    if (! $group) {
                        Notification::make()
                            ->danger()
                            ->title('Gruppo Non Trovato')
                            ->body('Il codice gruppo inserito non è valido.')
                            ->send();

                        return;
                    }

                    // Aggiungi l'utente al gruppo
                    $user->update(['dinner_group_id' => $group->id]);

                    Notification::make()
                        ->success()
                        ->title('Benvenuto!')
                        ->body("Ti sei unito al gruppo: {$group->name}")
                        ->send();

                    // Refresh della pagina
                    $this->redirect(static::getUrl());
                } catch (Halt $exception) {
                    return;
                }
            });
    }

    /**
     * Definisce l'azione per uscire dal gruppo corrente.
     *
     * Richiede conferma prima di rimuovere l'utente dal gruppo.
     * L'utente dovrà successivamente creare o unirsi a un altro gruppo.
     *
     * @return Action Azione configurata per l'uscita dal gruppo
     */
    public function leaveGroupAction(): Action
    {
        return Action::make('leaveGroup')
            ->label('Esci dal Gruppo')
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Conferma Uscita')
            ->modalDescription('Sei sicuro di voler uscire dal gruppo? Dovrai unirti nuovamente o crearne uno nuovo.')
            ->action(function (): void {
                try {
                    /** @var \App\Models\User $user */
                    $user = $this->getUser();

                    if (! $user->dinnerGroup) {
                        Notification::make()
                            ->warning()
                            ->title('Attenzione')
                            ->body('Non fai parte di nessun gruppo.')
                            ->send();

                        return;
                    }

                    // Rimuovi l'utente dal gruppo
                    $user->update(['dinner_group_id' => null]);

                    Notification::make()
                        ->success()
                        ->title('Uscita Completata')
                        ->body('Sei uscito dal gruppo.')
                        ->send();

                    // Refresh della pagina
                    $this->redirect(static::getUrl());
                } catch (Halt $exception) {
                    return;
                }
            });
    }

    /**
     * Ottiene le azioni da visualizzare nell'header della pagina.
     *
     * Se l'utente non appartiene a nessun gruppo, mostra le azioni
     * per crearne uno o unirsi a uno esistente. Se è già membro,
     * mostra solo l'azione per uscire dal gruppo.
     *
     * @return array Lista di azioni disponibili per l'header
     */
    protected function getHeaderActions(): array
    {
        $user = $this->getUser();

        // Se l'utente non è in un gruppo, mostra le azioni per creare o unirsi
        if (! $user->dinnerGroup) {
            return [
                $this->createGroupAction(),
                $this->joinGroupAction(),
            ];
        }

        // Se l'utente è già in un gruppo, mostra l'azione per uscire
        return [
            $this->leaveGroupAction(),
        ];
    }
}
