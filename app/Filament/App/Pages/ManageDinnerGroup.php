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

class ManageDinnerGroup extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.app.pages.manage-dinner-group';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Il Mio Gruppo';

    protected static ?string $title = 'Gestione Gruppo';

    public ?array $createData = [];

    public ?array $joinData = [];

    /**
     * Mount del componente - carica i dati iniziali
     */
    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * Ottiene l'utente corrente
     */
    protected function getUser()
    {
        return Auth::user();
    }

    /**
     * Ottiene il gruppo dell'utente corrente
     */
    protected function getUserGroup(): ?DinnerGroup
    {
        return $this->getUser()->dinnerGroup;
    }

    /**
     * Form per creare un nuovo gruppo
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
     * Form per unirsi a un gruppo esistente
     */
    protected function getJoinGroupFormSchema(): array
    {
        return [
            TextInput::make('group_code')
                ->label('Codice Gruppo')
                ->required()
                ->length(8)
                ->placeholder('XXXXXXXX')
                ->rule('exists:dinner_groups,group_code'),
        ];
    }

    /**
     * Azione per creare un nuovo gruppo
     */
    public function createGroupAction(): Action
    {
        return Action::make('createGroup')
            ->label('Crea Nuovo Gruppo')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->form($this->getCreateGroupFormSchema())
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
                        $groupCode = strtoupper(Str::random(8));
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
     * Azione per unirsi a un gruppo esistente
     */
    public function joinGroupAction(): Action
    {
        return Action::make('joinGroup')
            ->label('Unisciti a un Gruppo')
            ->icon('heroicon-o-arrow-right-circle')
            ->color('primary')
            ->form($this->getJoinGroupFormSchema())
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
     * Azione per uscire dal gruppo corrente
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
     * Ottiene le azioni della pagina
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
