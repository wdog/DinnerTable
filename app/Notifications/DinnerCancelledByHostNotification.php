<?php

namespace App\Notifications;

use Carbon\Carbon;
use App\Models\DinnerBooking;
use Illuminate\Bus\Queueable;
use App\Models\DinnerAvailability;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

/**
 * Notifica inviata agli ospiti quando l'host cancella la cena.
 *
 * Questa notifica viene inviata automaticamente tramite:
 * - Email (se configurato)
 * - Notifiche database per visualizzazione in-app
 * - Notifiche Filament per feedback immediato
 *
 * @see DinnerAvailabilityObserver Per la logica di invio
 */
class DinnerCancelledByHostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Crea una nuova istanza della notifica.
     *
     * @param  DinnerAvailability  $availability  Disponibilità cancellata dall'host
     * @param  DinnerBooking  $booking  Prenotazione cancellata
     */
    public function __construct(
        public DinnerAvailability $availability,
        public DinnerBooking $booking
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

        $reason = $this->availability->cancellation_reason
            ? $this->availability->cancellation_reason->getLabel()
            : 'Nessun motivo specificato';

        $profile = $host->profile;

        return (new MailMessage)
            ->subject('Cena cancellata - ' . $formattedDate)
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('Ci dispiace informarti che la cena a cui avevi prenotato è stata cancellata.')
            ->line('**Data:** ' . $formattedDate)
            ->line('**Host:** ' . $host->name)
            ->line('**Presso:** ' . $profile?->address . ' ' . $profile?->house_number . ', ' . $profile?->postal_code . ' ' . $profile?->city)
            ->line('**Motivo:** ' . $reason)
            ->line('La tua prenotazione è stata automaticamente cancellata e puoi ora prenotare per un\'altra cena disponibile.')
            ->action('Vedi disponibilità', url('/'))
            ->success()
            ->line('GNAM!')
            ->line('Grazie per la comprensione!');
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
            'title'               => 'Cena cancellata',
            'body'                => 'La cena del ' . $formattedDate . ' è stata cancellata dall\'host.',
            'availability_id'     => $this->availability->id,
            'booking_id'          => $this->booking->id,
            'dinner_date'         => $dinnerDate,
            'host_name'           => $this->availability->user->name,
            'cancellation_reason' => $this->availability->cancellation_reason?->value,
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        $dinnerDate    = $this->availability->dinnerDate->dinner_date;
        $formattedDate = Carbon::parse($dinnerDate)->isoFormat('dddd D MMMM YYYY');

        return FilamentNotification::make()
            ->title('Cena Cancellata')
            ->color('danger')
            ->danger()
            ->body('La cena del ' . $formattedDate . ' è stata cancellata dall\'host.')
            ->icon('tabler-chef-hat-off')
            ->iconColor('danger')
            ->getDatabaseMessage();
    }
}
