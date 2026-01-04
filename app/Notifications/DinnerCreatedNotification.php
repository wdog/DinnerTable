<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Models\DinnerAvailability;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

/**
 * Notifica inviata ai membri del gruppo quando un host crea una nuova disponibilità.
 *
 * Questa notifica viene inviata automaticamente tramite:
 * - Email (se configurato)
 * - Notifiche database per visualizzazione in-app
 *
 * @see DinnerAvailabilityObserver Per la logica di invio
 */
class DinnerCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Crea una nuova istanza della notifica.
     *
     * @param  DinnerAvailability  $availability  Disponibilità creata dall'host
     */
    public function __construct(
        public DinnerAvailability $availability
    ) {
        //
    }

    /**
     * Definisce i canali di invio della notifica.
     *
     * @param  object  $notifiable  Utente destinatario
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Rappresentazione email della notifica.
     *
     * @param  object  $notifiable  Utente destinatario
     */
    public function toMail(object $notifiable): MailMessage
    {
        $host          = $this->availability->user;
        $dinnerDate    = $this->availability->dinnerDate->dinner_date;
        $formattedDate = Carbon::parse($dinnerDate)->isoFormat('dddd D MMMM YYYY');
        $profile       = $host->profile;
        $dinnerName    = $this->availability->dinner_name ?? 'Cena';

        $mailMessage = (new MailMessage)
            ->subject('Nuova cena disponibile - ' . $formattedDate)
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('Buone notizie! C\'è una nuova cena disponibile nel tuo gruppo!')
            ->line('**Cena:** ' . $dinnerName)
            ->line('**Data:** ' . $formattedDate)
            ->line('**Host:** ' . $host->name)
            ->line('**Presso:** ' . $profile?->address . ' ' . $profile?->house_number . ', ' . $profile?->postal_code . ' ' . $profile?->city)
            ->line('**Posti disponibili:** ' . $this->availability->max_guests);

        if ($this->availability->note) {
            $mailMessage->line('**Note:** ' . $this->availability->note);
        }

        return $mailMessage
            ->action('Prenota ora', url('/dinner/group-availabilities'))
            ->success()
            ->line('GNAM!')
            ->line('Non perdere l\'occasione di partecipare!');
    }

    /**
     * Rappresentazione array della notifica per il database.
     *
     * @param  object  $notifiable  Utente destinatario
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $dinnerDate    = $this->availability->dinnerDate->dinner_date;
        $formattedDate = Carbon::parse($dinnerDate)->isoFormat('dddd D MMMM YYYY');

        return [
            'title'           => 'Nuova cena disponibile',
            'body'            => 'Una nuova cena è disponibile il ' . $formattedDate,
            'availability_id' => $this->availability->id,
            'dinner_date'     => $dinnerDate,
            'host_name'       => $this->availability->user->name,
            'max_guests'      => $this->availability->max_guests,
        ];
    }

    /**
     * Rappresentazione database della notifica per Filament.
     *
     * @param  object  $notifiable  Utente destinatario
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $dinnerDate    = $this->availability->dinnerDate->dinner_date;
        $formattedDate = Carbon::parse($dinnerDate)->isoFormat('dddd D MMMM YYYY');
        $dinnerName    = $this->availability->dinner_name ?? 'Cena';

        return FilamentNotification::make()
            ->title('Nuova Cena Disponibile!')
            ->color('success')
            ->success()
            ->body($dinnerName . ' del ' . $formattedDate . ' - ' . $this->availability->max_guests . ' posti')
            ->icon('tabler-chef-hat')
            ->iconColor('success')
            ->getDatabaseMessage();
    }
}
