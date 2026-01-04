<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

/**
 * Pagina per visualizzare tutte le notifiche dell'utente.
 *
 * Mostra cronologia completa delle notifiche ricevute (lette e non lette)
 * con supporto per filtro "solo nuovi" e gestione completa (lettura/eliminazione).
 *
 * Funzionalità:
 * - Badge navigazione con conteggio non lette
 * - Filtro toggle "Solo nuovi" (visibile solo se ci sono non lette)
 * - Paginazione (5 notifiche per pagina)
 * - Marca come letta (singola o tutte)
 * - Eliminazione (singola o tutte)
 * - Dark mode support con CSS light-dark()
 *
 * @see resources/views/filament/app/pages/view-notifications.blade.php
 */
class ViewNotifications extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'tabler-bell';

    protected static ?string $navigationLabel = 'Notifiche';

    protected static ?string $title = 'Le mie notifiche';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.app.pages.view-notifications';

    /**
     * Filtro per visualizzare solo notifiche non lette.
     *
     * Default: true (mostra solo nuovi messaggi all'apertura).
     * Toggle tramite pulsante "Mostra tutti" / "Mostra solo nuovi".
     */
    public bool $showOnlyUnread = true;

    /**
     * Ottiene badge con conteggio notifiche non lette.
     *
     * Mostra numero + emoji solo se ci sono notifiche non lette.
     *
     * @return string|null Badge con formato "N 💬" oppure null
     */
    public static function getNavigationBadge(): ?string
    {
        /** @var \App\Models\User|null $user */
        $user  = Auth::user();
        $count = $user?->unreadNotifications()->count();

        return $count > 0 ? (string) $count . '  💬' : null;
    }

    /**
     * Colore del badge navigazione.
     *
     * @return string Colore Filament (info = blu)
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    /**
     * Ottiene tutte le notifiche dell'utente corrente.
     *
     * Applica filtro "solo non lette" se $showOnlyUnread è true.
     * Ordina per data creazione decrescente (più recenti prima).
     * Paginazione: 5 notifiche per pagina.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Dispatch events per refresh UI
        $this->dispatch('refresh-sidebar');
        $this->dispatch('refresh');

        $query = $user->notifications();

        // Applica filtro solo non lette se attivo
        if ($this->showOnlyUnread) {
            $query->whereNull('read_at');
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate(5);
    }

    /**
     * Verifica se ci sono notifiche non lette.
     *
     * Usato per mostrare/nascondere il pulsante filtro "Solo nuovi".
     *
     * @return bool True se esistono notifiche non lette
     */
    public function hasUnreadNotifications(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->unreadNotifications()->exists();
    }

    /**
     * Toggle filtro solo non lette.
     *
     * Inverte il valore di $showOnlyUnread.
     * Livewire aggiorna automaticamente la vista.
     */
    public function toggleUnreadFilter(): void
    {
        $this->showOnlyUnread = ! $this->showOnlyUnread;
    }

    /**
     * Marca una notifica come letta.
     *
     * Trova la notifica per ID e la marca come letta.
     * Mostra toast di conferma.
     *
     * @param string $notificationId UUID della notifica
     */
    public function markAsRead(string $notificationId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notification = $user
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            Notification::make()
                ->title('Notifica marcata come letta')
                ->success()
                ->send();
        }
    }

    /**
     * Marca tutte le notifiche come lette.
     *
     * Marca come lette tutte le notifiche non lette dell'utente.
     * Dispatch $refresh per aggiornare badge e lista.
     */
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();

        Notification::make()
            ->title('Tutte le notifiche sono state marcate come lette')
            ->success()
            ->send();

        // Refresh per aggiornare il badge
        $this->dispatch('$refresh');
    }

    /**
     * Elimina tutte le notifiche dell'utente.
     *
     * Cancella tutte le notifiche (lette e non lette) dell'utente.
     * Usa single query per performance.
     * Dispatch $refresh per aggiornare badge e lista.
     */
    public function deleteAll(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->notifications()->delete();

        Notification::make()
            ->title('Tutte le notifiche sono state eliminate')
            ->success()
            ->send();

        // Refresh per aggiornare il badge
        $this->dispatch('$refresh');
    }

    /**
     * Elimina una notifica.
     *
     * Cancella singola notifica per ID.
     * Mostra toast di conferma.
     *
     * @param string $notificationId UUID della notifica
     */
    public function deleteNotification(string $notificationId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user
            ->notifications()
            ->where('id', $notificationId)
            ->delete();

        Notification::make()
            ->title('Notifica eliminata')
            ->success()
            ->send();
    }
}
