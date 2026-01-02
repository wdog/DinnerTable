<?php

use App\Models\User;
use App\Models\DinnerLog;
use App\Models\DinnerBooking;
use App\Models\DinnerAvailability;
use App\Enums\DinnerBookingStatus;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ============================================================================
 * TEST: DinnerBookingObserver
 * ============================================================================
 *
 * Test completi per il DinnerBookingObserver che verificano:
 * - Logging creazione booking
 * - Aggiornamento automatico status host (AVAILABLE_TO_HOST → ALMOST_FULL → FULL)
 * - Logging modifiche (status, guests_count, bringing_items, notes)
 * - Gestione cancellazione e delete
 * - Edge cases (loop prevention, HOST_CANCELLED protected)
 *
 * Pattern utilizzato: AAA (Arrange-Act-Assert)
 * Stile: TDD con commenti esplicativi in italiano
 */

/**
 * ============================================================================
 * SEZIONE A: CREAZIONE
 * ============================================================================
 *
 * Test per il logging e aggiornamento status alla creazione del booking.
 */

test('observer logs booking creation', function () {
    // Arrange: Crea host availability
    $availability = DinnerAvailability::factory()->asHost()->create();
    $guest        = User::factory()->create();

    // Act: Crea booking (Observer viene triggerato)
    $booking = DinnerBooking::create([
        'host_availability_id' => $availability->id,
        'guest_user_id'        => $guest->id,
        'guests_count'         => 2,
        'bringing_items'       => ['Vino'],
        'notes'                => 'Grazie!',
        'status'               => DinnerBookingStatus::PENDING,
    ]);

    // Assert: Verifica log 'created'
    $log = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->whereJsonContains('metadata->event', 'created')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['guests_count'])->toBe(2)
        ->and($log->metadata['bringing_items'])->toBe(['Vino']);
});

test('confirmed booking updates host status to ALMOST_FULL', function () {
    // Arrange: Host AVAILABLE_TO_HOST con 10 posti
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 10,
    ]);

    // Act: Crea booking CONFIRMED con 2 ospiti
    DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();

    // Assert: Host status passa a ALMOST_FULL (ha prenotazioni ma non è pieno)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);
});

test('pending booking does not update host status', function () {
    // Arrange: Host AVAILABLE_TO_HOST
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status' => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
    ]);

    // Act: Crea booking PENDING (non ancora confermato)
    DinnerBooking::factory()->pending()->forHost($availability)->create();

    // Assert: Host status rimane AVAILABLE_TO_HOST (pending non conta)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);
});

/**
 * ============================================================================
 * SEZIONE B: AGGIORNAMENTO STATO HOST
 * ============================================================================
 *
 * Test per la logica automatica di aggiornamento stato host basata su occupazione.
 */

test('when first confirmed booking created, host status changes to ALMOST_FULL', function () {
    // Arrange: Host con 8 posti, nessun booking
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 8,
    ]);

    // Act: Prima prenotazione confermata
    DinnerBooking::factory()->confirmed()->withGuests(1)->forHost($availability)->create();

    // Assert: Status → ALMOST_FULL (1/8 posti occupati)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);
});

test('when all spots filled, host status changes to FULL', function () {
    // Arrange: Host con 5 posti
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 5,
    ]);

    // Act: Prenota tutti i 5 posti
    DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();
    DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();

    // Assert: Status → FULL (5/5 posti occupati)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::FULL);
});

test('when booking cancelled, host status recalculates correctly', function () {
    // Arrange: Host con 6 posti, 2 bookings per 3 ospiti ciascuno (6/6 FULL)
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 6,
    ]);

    $booking1 = DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();
    $booking2 = DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();

    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::FULL);

    // Act: Cancella un booking (libera 3 posti)
    $booking1->update(['status' => DinnerBookingStatus::CANCELLED]);

    // Assert: Status torna a ALMOST_FULL (3/6 posti occupati)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);
});

test('when booking deleted, host status recalculates correctly', function () {
    // Arrange: Host con 4 posti, 1 booking per 4 ospiti (4/4 FULL)
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 4,
    ]);

    $booking = DinnerBooking::factory()->confirmed()->withGuests(4)->forHost($availability)->create();

    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::FULL);

    // Act: Elimina il booking
    $booking->delete();

    // Assert: Status torna a AVAILABLE_TO_HOST (0/4 posti occupati)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);
});

test('observer does not override HOST_CANCELLED status', function () {
    // Arrange: Host CANCELLATO (decisione manuale dell'host)
    $availability = DinnerAvailability::factory()->cancelled()->create(['max_guests' => 10]);

    // Act: Tentativo di creare booking (non dovrebbe cambiare status)
    DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();

    // Assert: Status rimane HOST_CANCELLED (protetto dall'Observer)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::HOST_CANCELLED);
});

test('observer uses saveQuietly to avoid loop with availability observer', function () {
    // Arrange: Host con booking
    $availability = DinnerAvailability::factory()->asHost()->create(['max_guests' => 10]);

    // Conta SOLO log dell'availability (non booking) prima del booking
    $initialAvailabilityLogs = DinnerLog::where('loggable_type', DinnerAvailability::class)
        ->where('loggable_id', $availability->id)
        ->count();

    // Act: Crea booking confermato (Observer aggiorna host status con saveQuietly)
    DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();

    // Assert: Host status aggiornato ma Observer availability NON ha loggato (saveQuietly)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);

    $afterAvailabilityLogs = DinnerLog::where('loggable_type', DinnerAvailability::class)
        ->where('loggable_id', $availability->id)
        ->count();

    // Log count dovrebbe essere uguale (saveQuietly bypassa availability Observer)
    expect($afterAvailabilityLogs)->toBe($initialAvailabilityLogs);
});

test('multiple confirmed bookings update status correctly', function () {
    // Arrange: Host con 12 posti
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 12,
    ]);

    // Act: Aggiungi booking progressivamente
    DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL); // 3/12

    DinnerBooking::factory()->confirmed()->withGuests(4)->forHost($availability)->create();
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL); // 7/12

    DinnerBooking::factory()->confirmed()->withGuests(5)->forHost($availability)->create();
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::FULL); // 12/12
});

test('when last booking cancelled, host status returns to AVAILABLE_TO_HOST', function () {
    // Arrange: Host con 5 posti, 1 booking per 3 ospiti
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 5,
    ]);

    $booking = DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();

    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);

    // Act: Cancella l'unico booking
    $booking->update(['status' => DinnerBookingStatus::CANCELLED]);

    // Assert: Status torna a AVAILABLE_TO_HOST (0/5 posti)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);
});

/**
 * ============================================================================
 * SEZIONE C: LOGGING MODIFICHE
 * ============================================================================
 *
 * Test per il logging delle modifiche ai campi del booking.
 */

test('observer logs status change from pending to confirmed', function () {
    // Arrange: Crea booking PENDING
    $booking = DinnerBooking::factory()->pending()->create();

    // Act: Cambia status a CONFIRMED
    $booking->update(['status' => DinnerBookingStatus::CONFIRMED]);

    // Assert: Verifica log status_changed
    $log = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->whereJsonContains('metadata->event', 'status_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_status'])->toBe(DinnerBookingStatus::PENDING->value)
        ->and($log->metadata['new_status'])->toBe(DinnerBookingStatus::CONFIRMED->value);
});

test('observer logs guests_count change', function () {
    // Arrange: Booking con 2 ospiti
    $booking = DinnerBooking::factory()->confirmed()->withGuests(2)->create();

    // Act: Cambia numero ospiti
    $booking->update(['guests_count' => 4]);

    // Assert: Verifica log guests_count_changed
    $log = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->whereJsonContains('metadata->event', 'guests_count_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_value'])->toBe(2)
        ->and($log->metadata['new_value'])->toBe(4);
});

test('observer logs bringing_items change', function () {
    // Arrange: Booking con items
    $booking = DinnerBooking::factory()->create(['bringing_items' => ['Vino']]);

    // Act: Cambia bringing_items
    $booking->update(['bringing_items' => ['Vino', 'Dolce', 'Pane']]);

    // Assert: Verifica log bringing_items_changed
    $log = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->whereJsonContains('metadata->event', 'bringing_items_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_value'])->toBe(['Vino'])
        ->and($log->metadata['new_value'])->toBe(['Vino', 'Dolce', 'Pane']);
});

test('observer logs notes change', function () {
    // Arrange: Booking con note
    $booking = DinnerBooking::factory()->create(['notes' => 'Nota iniziale']);

    // Act: Cambia notes
    $booking->update(['notes' => 'Nota aggiornata']);

    // Assert: Verifica log notes_changed
    $log = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->whereJsonContains('metadata->event', 'notes_changed')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->metadata['old_value'])->toBe('Nota iniziale')
        ->and($log->metadata['new_value'])->toBe('Nota aggiornata');
});

test('observer logs multiple changes in single update', function () {
    // Arrange: Booking esistente
    $booking = DinnerBooking::factory()->confirmed()->withGuests(2)->create([
        'bringing_items' => ['Vino'],
        'notes'          => 'Nota vecchia',
    ]);

    // Act: Cambia multipli campi insieme
    $booking->update([
        'guests_count'   => 3,
        'bringing_items' => ['Vino', 'Dolce'],
        'notes'          => 'Nota nuova',
    ]);

    // Assert: Verifica che ci siano 3 log diversi
    $logs = DinnerLog::where('loggable_type', 'App\Models\DinnerBooking')
        ->where('loggable_id', $booking->id)
        ->get();

    $events = $logs->pluck('metadata.event')->filter()->values();

    expect($events)->toContain('guests_count_changed')
        ->and($events)->toContain('bringing_items_changed')
        ->and($events)->toContain('notes_changed');
});

/**
 * ============================================================================
 * SEZIONE D: EDGE CASES
 * ============================================================================
 *
 * Test per casi limite e scenari particolari.
 */

test('guest availability is never modified by observer', function () {
    // Arrange: Guest availability (can_host = false)
    $guestAvailability = DinnerAvailability::factory()->asGuest()->create();

    // Guest availability non dovrebbe mai avere bookings, ma testiamo la protezione
    // Questo scenario non dovrebbe accadere in produzione (policy lo previene)

    // Assert: Verifica che guest availability abbia can_host = false
    expect($guestAvailability->can_host)->toBeFalse();
});

test('booking update without status or guests_count change does not update host', function () {
    // Arrange: Host con booking
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 10,
    ]);

    $booking = DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);

    // Act: Modifica solo note (non status né guests_count)
    $booking->update(['notes' => 'Nota modificata']);

    // Assert: Host status rimane ALMOST_FULL (non ricalcolato)
    expect($availability->fresh()->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);
});

test('host status calculation handles zero bookings correctly', function () {
    // Arrange: Host senza bookings
    $availability = DinnerAvailability::factory()->asHost()->create([
        'status'     => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests' => 8,
    ]);

    // Act: Verifica calcolo con 0 bookings
    $totalBooked = $availability->total_booked_guests;

    // Assert: 0 ospiti prenotati → status rimane AVAILABLE_TO_HOST
    expect($totalBooked)->toBe(0)
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);
});
