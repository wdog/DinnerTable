<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

/**
 * Pagina per visualizzare tutte le notifiche dell'utente.
 *
 * Mostra cronologia completa delle notifiche ricevute (lette e non lette).
 */
class ViewNotifications extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'tabler-bell';

    protected string $view = 'filament.app.pages.view-notifications';

    protected static ?string $navigationLabel = 'Notifiche';

    protected static ?string $title = 'Le mie notifiche';

    protected static ?int $navigationSort = 10;

    /**
     * Ottiene badge con conteggio notifiche non lette.
     */
    public static function getNavigationBadge(): ?string
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $count =   $user?->unreadNotifications()->count();


        return $count > 0 ? (string)  $count  . "  💬" : null;
    }



    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    /**
     * Ottiene tutte le notifiche dell'utente corrente.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(5);
    }

    /**
     * Marca una notifica come letta.
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
