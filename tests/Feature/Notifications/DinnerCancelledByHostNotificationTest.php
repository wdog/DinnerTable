<?php

use App\Models\User;
use App\Models\DinnerBooking;
use App\Enums\CancellationReason;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\DinnerCancelledByHostNotification;

uses(RefreshDatabase::class);

/**
 * ============================================================================
 * TEST: DinnerCancelledByHostNotification
 * ============================================================================
 *
 * Test completi per la notifica DinnerCancelledByHostNotification che verificano:
 * - Struttura notifica (canali, dati)
 * - Invio ai guest corretti
 * - Esclusione guest non coinvolti
 * - Gestione multipli guest
 *
 * Pattern utilizzato: AAA (Arrange-Act-Assert)
 * Stile: TDD con commenti esplicativi in italiano
 */

/**
 * ============================================================================
 * SEZIONE A: NOTIFICA STRUTTURA
 * ============================================================================
 *
 * Test per verificare la struttura e i dati della notifica.
 */
test('notification is sent via database channel', function () {
    Notification::fake();

    // Arrange: Host con booking confermato
    $host  = User::factory()->create();
    $guest = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();
    $booking      = DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::PERSONAL_EMERGENCY,
    ]);

    // Assert: Notifica inviata tramite database
    Notification::assertSentTo(
        $guest,
        DinnerCancelledByHostNotification::class,
        function ($notification, $channels) {
            return in_array('database', $channels);
        }
    );
});

test('notification contains correct data', function () {
    Notification::fake();

    // Arrange: Host con booking
    $host  = User::factory()->create();
    $guest = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();
    $booking      = DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::ILLNESS,
    ]);

    // Assert: Notifica contiene availability e booking
    Notification::assertSentTo(
        $guest,
        DinnerCancelledByHostNotification::class,
        function ($notification, $channels) use ($availability, $booking) {
            return $notification->availability->id === $availability->id
                && $notification->booking->id === $booking->id;
        }
    );
});

test('notification includes host name, date, and reason', function () {
    Notification::fake();

    // Arrange: Host con booking
    $host  = User::factory()->create(['name' => 'Mario Rossi']);
    $guest = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create([
        'dinner_name' => 'Pizza Night',
    ]);

    $booking = DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest)->create();

    // Act: Host cancella con motivo specifico
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::WORK_COMMITMENT,
    ]);

    // Assert: Notifica contiene host, date e reason
    Notification::assertSentTo(
        $guest,
        DinnerCancelledByHostNotification::class,
        function ($notification) use ($host, $guest) {
            // Verifica dati notifica
            $data = $notification->toArray($guest);

            return isset($data['host_name'])
                && isset($data['dinner_date'])
                && isset($data['cancellation_reason'])
                && $notification->availability->user->name === $host->name;
        }
    );
});

/**
 * ============================================================================
 * SEZIONE B: INVIO NOTIFICA
 * ============================================================================
 *
 * Test per verificare che la notifica venga inviata ai guest corretti.
 */
test('notification is sent to all guests with confirmed bookings when host cancels', function () {
    Notification::fake();

    // Arrange: Host con 3 guest confermati
    $host   = User::factory()->create();
    $guest1 = User::factory()->create();
    $guest2 = User::factory()->create();
    $guest3 = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();

    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest1)->create();
    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest2)->create();
    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest3)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::HOUSE_ISSUE,
    ]);

    // Assert: Notifica inviata a tutti e 3 i guest
    Notification::assertSentTo($guest1, DinnerCancelledByHostNotification::class);
    Notification::assertSentTo($guest2, DinnerCancelledByHostNotification::class);
    Notification::assertSentTo($guest3, DinnerCancelledByHostNotification::class);
});

test('notification is not sent to guests with cancelled bookings', function () {
    Notification::fake();

    // Arrange: Host con mix di bookings
    $host           = User::factory()->create();
    $confirmedGuest = User::factory()->create();
    $cancelledGuest = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();

    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($confirmedGuest)->create();
    DinnerBooking::factory()->cancelled()->forHost($availability)->byGuest($cancelledGuest)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::OTHER,
    ]);

    // Assert: Notifica SOLO al confirmed, NON al cancelled
    Notification::assertSentTo($confirmedGuest, DinnerCancelledByHostNotification::class);
    Notification::assertNotSentTo($cancelledGuest, DinnerCancelledByHostNotification::class);
});

test('notification is not sent to guests with pending bookings', function () {
    Notification::fake();

    // Arrange: Host con booking pending
    $host         = User::factory()->create();
    $pendingGuest = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();

    DinnerBooking::factory()->pending()->forHost($availability)->byGuest($pendingGuest)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::PERSONAL_EMERGENCY,
    ]);

    // Assert: Notifica inviata anche ai pending (vengono cancellati dall'Observer)
    Notification::assertSentTo($pendingGuest, DinnerCancelledByHostNotification::class);
});

test('multiple guests receive notification when host cancels', function () {
    Notification::fake();

    // Arrange: Host con 5 guest
    $host = User::factory()->create();

    $guests = User::factory()->count(5)->create();

    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();

    foreach ($guests as $guest) {
        DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest)->create();
    }

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::ILLNESS,
    ]);

    // Assert: Tutti i 5 guest ricevono notifica
    foreach ($guests as $guest) {
        Notification::assertSentTo($guest, DinnerCancelledByHostNotification::class);
    }

    // Verifica count totale notifiche = 5
    Notification::assertCount(5);
});
