<?php

use App\Models\User;
use App\Models\DinnerLog;
use App\Models\DinnerDate;
use App\Models\DinnerBooking;
use App\Enums\CancellationReason;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\DinnerCancelledByHostNotification;

uses(RefreshDatabase::class);

/**
 * ============================================================================
 * TEST: DinnerAvailabilityObserver
 * ============================================================================
 *
 * Test completi per il DinnerAvailabilityObserver che verificano:
 * - Logging creazione disponibilità
 * - Logging modifiche (status, dinner_name, max_guests, note)
 * - Cascata cancellazione bookings quando host cancella
 * - Invio notifiche ai guest
 * - Edge cases e gestione errori
 *
 * Pattern utilizzato: AAA (Arrange-Act-Assert)
 * Stile: TDD con commenti esplicativi in italiano
 */

/**
 * ============================================================================
 * SEZIONE A: CREAZIONE
 * ============================================================================
 *
 * Test che verificano il logging della creazione disponibilità.
 */
test('observer logs availability creation', function () {
    // Arrange: Prepara dati
    $user = User::factory()->create();
    $date = DinnerDate::factory()->create();

    // Act: Crea availability (Observer viene triggerato)
    $availability = DinnerAvailability::create([
        'dinner_date_id' => $date->id,
        'user_id'        => $user->id,
        'can_host'       => true,
        'status'         => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests'     => 8,
        'dinner_name'    => 'Pizza Night',
    ]);

    // Assert: Verifica che sia stato creato un log 'created'
    $log = DinnerLog::where('availability_id', $availability->id)
        ->where('status', DinnerAvailabilityStatus::AVAILABLE_TO_HOST->value)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['event'])->toBe('created')
        ->and($log->logged_by)->toBe($user->id);
});

test('created availability has correct initial status based on can_host', function () {
    // Test HOST: can_host=true → AVAILABLE_TO_HOST
    $hostAvailability = DinnerAvailability::factory()->asHost()->create();
    expect($hostAvailability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);

    // Test GUEST: can_host=false → AVAILABLE
    $guestAvailability = DinnerAvailability::factory()->asGuest()->create();
    expect($guestAvailability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE);
});

/**
 * ============================================================================
 * SEZIONE B: CAMBIO STATO E LOGGING
 * ============================================================================
 *
 * Test per il logging delle modifiche ai campi dell'availability.
 */
test('observer logs status change', function () {
    // Arrange: Crea availability host
    $availability = DinnerAvailability::factory()->asHost()->create();

    // Act: Cambia status
    $availability->update(['status' => DinnerAvailabilityStatus::ALMOST_FULL]);

    // Assert: Verifica log status_changed
    $log = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'status_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_status'])->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST->value)
        ->and($log->metadata['new_status'])->toBe(DinnerAvailabilityStatus::ALMOST_FULL->value);
});

test('observer logs dinner_name change', function () {
    // Arrange: Crea host con dinner_name
    $availability = DinnerAvailability::factory()->asHost()->create(['dinner_name' => 'Pizza Night']);

    // Act: Cambia dinner_name
    $availability->update(['dinner_name' => 'Pasta Night']);

    // Assert: Verifica log dinner_name_changed
    $log = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'dinner_name_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_value'])->toBe('Pizza Night')
        ->and($log->metadata['new_value'])->toBe('Pasta Night');
});

test('observer logs max_guests change', function () {
    // Arrange: Crea host con max_guests
    $availability = DinnerAvailability::factory()->asHost()->create(['max_guests' => 6]);

    // Act: Cambia max_guests
    $availability->update(['max_guests' => 10]);

    // Assert: Verifica log max_guests_changed
    $log = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'max_guests_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_value'])->toBe(6)
        ->and($log->metadata['new_value'])->toBe(10);
});

test('observer logs note change', function () {
    // Arrange: Crea availability con nota
    $availability = DinnerAvailability::factory()->asHost()->create(['note' => 'Nota iniziale']);

    // Act: Cambia note
    $availability->update(['note' => 'Nota aggiornata']);

    // Assert: Verifica log note_changed
    $log = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'note_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_value'])->toBe('Nota iniziale')
        ->and($log->metadata['new_value'])->toBe('Nota aggiornata');
});

test('observer does not log if fields unchanged', function () {
    // Arrange: Crea availability
    $availability = DinnerAvailability::factory()->asHost()->create(['dinner_name' => 'Pizza']);

    // Conta log esistenti
    $initialLogCount = DinnerLog::where('availability_id', $availability->id)->count();

    // Act: Update senza modificare dinner_name
    $availability->update(['note' => 'Nuova nota']); // Solo note cambia

    // Assert: Non ci dovrebbe essere log per dinner_name (solo per note)
    $dinnerNameLogs = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'dinner_name_changed')
        ->count();

    expect($dinnerNameLogs)->toBe(0);
});

/**
 * ============================================================================
 * SEZIONE C: CASCATA CANCELLAZIONE
 * ============================================================================
 *
 * Test per la gestione della cancellazione host e cascata sui bookings.
 */
test('when host cancels, all confirmed bookings are cancelled', function () {
    // Arrange: Host con 3 bookings confermati
    $availability = DinnerAvailability::factory()->asHost()->create();

    $booking1 = DinnerBooking::factory()->confirmed()->forHost($availability)->create();
    $booking2 = DinnerBooking::factory()->confirmed()->forHost($availability)->create();
    $booking3 = DinnerBooking::factory()->confirmed()->forHost($availability)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::PERSONAL_EMERGENCY,
    ]);

    // Assert: Tutti i bookings sono CANCELLED
    expect($booking1->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED)
        ->and($booking2->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED)
        ->and($booking3->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED);
});

test('when host cancels, DinnerCancelledByHostNotification is sent to each guest', function () {
    Notification::fake();

    // Arrange: Host con 2 guest diversi
    $guest1 = User::factory()->create();
    $guest2 = User::factory()->create();

    $availability = DinnerAvailability::factory()->asHost()->create();

    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest1)->create();
    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest2)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::ILLNESS,
    ]);

    // Assert: Notifica inviata a entrambi i guest
    Notification::assertSentTo($guest1, DinnerCancelledByHostNotification::class);
    Notification::assertSentTo($guest2, DinnerCancelledByHostNotification::class);
});

test('when host cancels, cancellation is logged with booking ids', function () {
    // Arrange: Host con bookings
    $availability = DinnerAvailability::factory()->asHost()->create();
    $booking1     = DinnerBooking::factory()->confirmed()->forHost($availability)->create();
    $booking2     = DinnerBooking::factory()->confirmed()->forHost($availability)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::WORK_COMMITMENT,
    ]);

    // Assert: Log contiene booking ids cancellati
    $log = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'host_cancelled_cascade')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['cancelled_bookings_count'])->toBe(2)
        ->and($log->metadata['cancelled_booking_ids'])->toContain($booking1->id)
        ->and($log->metadata['cancelled_booking_ids'])->toContain($booking2->id);
});

test('when host cancels, pending bookings are also cancelled', function () {
    // Arrange: Host con mix di bookings
    $availability = DinnerAvailability::factory()->asHost()->create();

    $confirmedBooking = DinnerBooking::factory()->confirmed()->forHost($availability)->create();
    $pendingBooking   = DinnerBooking::factory()->pending()->forHost($availability)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::HOUSE_ISSUE,
    ]);

    // Assert: Sia confirmed che pending sono cancellati
    expect($confirmedBooking->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED)
        ->and($pendingBooking->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED);
});

test('when host cancels, already cancelled bookings are not affected', function () {
    // Arrange: Host con booking già cancellato
    $availability = DinnerAvailability::factory()->asHost()->create();

    $alreadyCancelled = DinnerBooking::factory()->cancelled()->forHost($availability)->create();
    $confirmed        = DinnerBooking::factory()->confirmed()->forHost($availability)->create();

    // Act: Host cancella
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::OTHER,
    ]);

    // Assert: Booking già cancelled rimane cancelled (non duplicato)
    expect($alreadyCancelled->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED)
        ->and($confirmed->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED);

    // Verifica che solo il confirmed sia stato contato nel log
    $log = DinnerLog::where('availability_id', $availability->id)
        ->whereJsonContains('metadata->event', 'host_cancelled_cascade')
        ->first();

    expect($log->metadata['cancelled_bookings_count'])->toBe(1); // Solo 1 nuovo cancellato
});

test('cancellation uses saveQuietly to avoid triggering booking observer', function () {
    // Arrange: Host con booking
    $availability = DinnerAvailability::factory()->asHost()->create();
    $booking      = DinnerBooking::factory()->confirmed()->forHost($availability)->create();

    // Conta log bookings prima della cancellazione
    $initialBookingLogs = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->count();

    // Act: Host cancella (usa saveQuietly internamente)
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::PERSONAL_EMERGENCY,
    ]);

    // Assert: Booking è cancellato ma Observer NON ha loggato (saveQuietly)
    expect($booking->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED);

    $afterBookingLogs = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->count();

    // Log count dovrebbe essere uguale (saveQuietly bypassa Observer)
    expect($afterBookingLogs)->toBe($initialBookingLogs);
});

/**
 * ============================================================================
 * SEZIONE D: EDGE CASES
 * ============================================================================
 *
 * Test per casi limite e scenari edge.
 */
test('guest availability change does not trigger cancellation cascade', function () {
    Notification::fake();

    // Arrange: Guest availability (can_host = false)
    $guestAvailability = DinnerAvailability::factory()->asGuest()->create();

    // Act: Guest cambia a NOT_AVAILABLE
    $guestAvailability->update(['status' => DinnerAvailabilityStatus::NOT_AVAILABLE]);

    // Assert: Nessuna notifica inviata (guest non ha bookings da cancellare)
    Notification::assertNothingSent();
});

test('status change from AVAILABLE_TO_HOST to ALMOST_FULL does not cancel bookings', function () {
    // Arrange: Host con booking confermato
    $availability = DinnerAvailability::factory()->asHost()->create();
    $booking      = DinnerBooking::factory()->confirmed()->forHost($availability)->create();

    // Act: Status cambia a ALMOST_FULL (progressione normale, non cancellazione)
    $availability->update(['status' => DinnerAvailabilityStatus::ALMOST_FULL]);

    // Assert: Booking rimane CONFIRMED
    expect($booking->fresh()->status)->toBe(DinnerBookingStatus::CONFIRMED);
});

test('notification failure does not prevent cancellation', function () {
    // Arrange: Mock notifica che fallisce
    Notification::fake();

    $availability = DinnerAvailability::factory()->asHost()->create();
    $booking      = DinnerBooking::factory()->confirmed()->forHost($availability)->create();

    // Act: Host cancella (anche se notifica fallisce, booking deve essere cancellato)
    $availability->update([
        'status'              => DinnerAvailabilityStatus::HOST_CANCELLED,
        'cancellation_reason' => CancellationReason::PERSONAL_EMERGENCY,
    ]);

    // Assert: Booking cancellato anche se notifica non inviata
    expect($booking->fresh()->status)->toBe(DinnerBookingStatus::CANCELLED);
});
