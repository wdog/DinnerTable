<?php

namespace App\Filament\App\Pages;

use App\Models\DinnerGroup;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Pagina per la gestione dei gruppi cena.
 *
 * Permette agli utenti di creare un nuovo gruppo cena, unirsi a un gruppo
 * esistente tramite codice, o uscire dal proprio gruppo corrente.
 * Ogni utente può appartenere a un solo gruppo alla volta.
 */
class ManageDinnerGroup extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    /**
     * Nome della vista Blade da utilizzare.
     */
    protected string $view = 'filament.app.pages.manage-dinner-group';

    /**
     * Icona di navigazione della pagina.
     */
    protected static string|BackedEnum|null $navigationIcon = 'tabler-chef-hat';

    /**
     * Etichetta del link di navigazione.
     */
    protected static ?string $navigationLabel = 'Il Mio Gruppo Cena';

    /**
     * Titolo della pagina.
     */
    protected static ?string $title = 'Gestione Gruppo';

    /**
     * Dati del form per la creazione di un gruppo.
     */
    public ?array $createData = [];

    /**
     * Dati del form per unirsi a un gruppo.
     */
    public ?array $joinData = [];

    /**
     * Inizializza il componente e carica i dati iniziali.
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
            ->icon('tabler-plus-circle')
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
            ->icon('tabler-door-enter')
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
            ->icon('tabler-door-exit')
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
     * Se l'utente non appartiene a nessun gruppo, non mostra nulla.
     * Se è già membro, mostra solo l'azione per uscire dal gruppo.
     *
     * @return array Lista di azioni disponibili per l'header
     */
    protected function getHeaderActions(): array
    {
        $user = $this->getUser();

        // Se l'utente non è in un gruppo, nessuna azione nell'header
        if (! $user->dinnerGroup) {
            return [];
        }

        // Se l'utente è già in un gruppo, mostra l'azione per uscire
        return [
            $this->leaveGroupAction(),
        ];
    }

    /**
     * Configura la tabella per visualizzare i membri del gruppo.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereNotNull('dinner_group_id')
                    ->where('dinner_group_id', $this->getUserGroup()?->id)
                    ->with(['profile', 'dinnerGroup'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->email)
                    ->icon(
                        fn (User $record): ?string => $record->id === $this->getUserGroup()?->created_by
                            ? 'tabler-crown'
                            : 'tabler-user'
                    )
                    ->iconColor(
                        fn (User $record): string => $record->id === $this->getUserGroup()?->created_by
                            ? 'warning'
                            : 'gray'
                    ),

                Tables\Columns\TextColumn::make('profile.city')
                    ->label('Città')
                    ->searchable()
                    ->sortable()
                    ->icon('tabler-map-pin')
                    ->iconColor('danger')
                    ->placeholder('Non specificata'),

                Tables\Columns\TextColumn::make('profile.max_guests')
                    ->label('Max Ospiti')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->icon('tabler-users-group')
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Membro dal')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->icon('tabler-calendar-event')
                    ->iconColor('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_creator')
                    ->label('Creatore')
                    ->boolean()
                    ->state(
                        fn (User $record): bool => $record->id === $this->getUserGroup()?->created_by
                    )
                    ->trueIcon('tabler-crown')
                    ->falseIcon('tabler-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_you')
                    ->label('Tu')
                    ->boolean()
                    ->state(
                        fn (User $record): bool => $record->id === $this->getUser()->id
                    )
                    ->trueIcon('tabler-user-check')
                    ->falseIcon('tabler-user')
                    ->trueColor('info')
                    ->falseColor('gray'),
            ])
            ->filters([

                Tables\Filters\Filter::make('high_capacity')
                    ->label('Alta Capacità (4+ ospiti)')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            'profile',
                            fn ($q) => $q->where('max_guests', '>=', 4)
                        )
                    )
                    ->toggle(),

                Tables\Filters\SelectFilter::make('city')
                    ->label('Città')
                    ->options(function () {
                        return \App\Models\Profile::query()
                            ->whereNotNull('city')
                            ->distinct()
                            ->pluck('city', 'city');
                    })
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query) => $query->whereHas(
                                'profile',
                                fn ($q) => $q->where('city', $data['value'])
                            )
                        );
                    })
                    ->searchable(),
            ])
            ->emptyStateHeading('Nessun membro nel gruppo')
            ->emptyStateDescription('Il gruppo non ha ancora membri.')
            ->emptyStateIcon('tabler-users-off')
            ->defaultSort('created_at', 'asc')
            ->poll('30s')
            ->striped();
    }

    /**
     * Metodo wrapper per chiamare l'action createGroup
     */
    public function openCreateGroupModal()
    {
        $this->mountAction('createGroupAction');
    }

    /**
     * Metodo wrapper per chiamare l'action joinGroup
     */
    public function openJoinGroupModal()
    {
        $this->mountAction('joinGroupAction');
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getUserGroup()
            ? 'Il Mio Gruppo Cena'
            : 'Unisciti o Crea un Gruppo';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if ($group = $this->getUserGroup()) {
            return "Gruppo: {$group->name}";
        }

        return 'Per iniziare, crea un nuovo gruppo o unisciti a uno esistente.';
    }
}
