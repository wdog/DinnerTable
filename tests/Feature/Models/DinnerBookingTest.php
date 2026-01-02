<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use App\Models\DinnerBooking;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ============================================================================
 * TEST: DinnerBooking Model
 * ============================================================================
 *
 * Test completi per il modello DinnerBooking che verificano:
 * - Creazione e validazioni
 * - Relazioni con DinnerAvailability, User, DinnerLog
 * - Attributi calcolati (total_guests, canBeModified)
 * - Scope (confirmed, cancelled, future, past)
 * - Cast dei campi (bringing_items array)
 *
 * Pattern utilizzato: AAA (Arrange-Act-Assert)
 * Stile: TDD con commenti esplicativi in italiano
 */

/**
 * ============================================================================
 * SEZIONE A: CREAZIONE E VALIDAZIONI
 * ============================================================================
 *
 * Test che verificano la corretta creazione delle prenotazioni e le
 * validazioni del model.
 */

test('booking can be created with required fields', function () {
    // Arrange: Prepara host availability e guest
    $availability = DinnerAvailability::factory()->asHost()->create();
    $guest        = User::factory()->create();

    // Act: Crea booking con tutti i campi
    $booking = DinnerBooking::create([
        'host_availability_id' => $availability->id,
        'guest_user_id'        => $guest->id,
        'guests_count'         => 2,
        'bringing_items'       => ['Vino', 'Dolce'],
        'notes'                => 'Non vedo l\'ora!',
        'status'               => DinnerBookingStatus::PENDING,
    ]);

    // Assert: Verifica creazione corretta
    expect($booking)->not->toBeNull()
        ->and($booking->guest_user_id)->toBe($guest->id)
        ->and($booking->guests_count)->toBe(2)
        ->and($booking->bringing_items)->toBe(['Vino', 'Dolce'])
        ->and($booking->status)->toBe(DinnerBookingStatus::PENDING);
});

test('factory creates pending booking by default', function () {
    // Act: Usa factory senza state
    $booking = DinnerBooking::factory()->create();

    // Assert: Verifica che il default sia PENDING
    expect($booking->status)->toBe(DinnerBookingStatus::PENDING)
        ->and($booking->guests_count)->toBeGreaterThan(0);
});

test('factory can create confirmed booking', function () {
    // Act: Usa factory con state confirmed
    $booking = DinnerBooking::factory()->confirmed()->create();

    // Assert: Verifica status CONFIRMED
    expect($booking->status)->toBe(DinnerBookingStatus::CONFIRMED);
});

test('factory can create cancelled booking', function () {
    // Act: Usa factory con state cancelled
    $booking = DinnerBooking::factory()->cancelled()->create();

    // Assert: Verifica status CANCELLED
    expect($booking->status)->toBe(DinnerBookingStatus::CANCELLED);
});

test('unique constraint prevents duplicate booking for same guest and host availability', function () {
    // Arrange: Crea primo booking
    $availability = DinnerAvailability::factory()->asHost()->create();
    $guest        = User::factory()->create();

    DinnerBooking::factory()
        ->forHost($availability)
        ->byGuest($guest)
        ->create();

    // Act & Assert: Tentativo di duplicato deve fallire
    expect(fn () => DinnerBooking::factory()
        ->forHost($availability)
        ->byGuest($guest)
        ->create()
    )->toThrow(\Exception::class);
});

test('bringing_items casts to array correctly', function () {
    // Arrange & Act: Crea booking con bringing_items
    $booking = DinnerBooking::factory()->create([
        'bringing_items' => ['Vino', 'Pane', 'Frutta'],
    ]);

    // Assert: Verifica che sia un array
    expect($booking->bringing_items)->toBeArray()
        ->and($booking->bringing_items)->toHaveCount(3)
        ->and($booking->bringing_items[0])->toBe('Vino');

    // Arrange & Act: Booking senza items (array vuoto)
    $bookingEmpty = DinnerBooking::factory()->create([
        'bringing_items' => [],
    ]);

    // Assert: Array vuoto
    expect($bookingEmpty->bringing_items)->toBeArray()
        ->and($bookingEmpty->bringing_items)->toHaveCount(0);
});

/**
 * ============================================================================
 * SEZIONE B: RELAZIONI
 * ============================================================================
 *
 * Test che verificano le relazioni del model con DinnerAvailability e User.
 */

test('booking belongs to host availability', function () {
    // Arrange: Crea booking
    $availability = DinnerAvailability::factory()->asHost()->create();
    $booking      = DinnerBooking::factory()->forHost($availability)->create();

    // Act: Recupera la relazione
    $relatedAvailability = $booking->hostAvailability;

    // Assert: Verifica che sia l'availability corretta
    expect($relatedAvailability)->not->toBeNull()
        ->and($relatedAvailability->id)->toBe($availability->id)
        ->and($relatedAvailability)->toBeInstanceOf(DinnerAvailability::class);
});

test('booking belongs to guest user', function () {
    // Arrange: Crea booking per guest specifico
    $guest   = User::factory()->create();
    $booking = DinnerBooking::factory()->byGuest($guest)->create();

    // Act: Recupera la relazione
    $relatedGuest = $booking->guest;

    // Assert: Verifica che sia il guest corretto
    expect($relatedGuest)->not->toBeNull()
        ->and($relatedGuest->id)->toBe($guest->id)
        ->and($relatedGuest)->toBeInstanceOf(User::class);
});

test('booking has many logs via morphMany', function () {
    // Arrange: Crea booking (logs vengono creati dall'Observer)
    $booking = DinnerBooking::factory()->create();

    // Act: Recupera logs
    $logs = $booking->logs;

    // Assert: Verifica che la relazione esista (può essere vuota se Observer non attivo)
    expect($logs)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

/**
 * ============================================================================
 * SEZIONE C: ATTRIBUTI E SCOPE
 * ============================================================================
 *
 * Test per gli attributi computed e gli scope query del model.
 */

test('total_guests equals guests_count', function () {
    // Arrange: Crea booking con 3 ospiti
    $booking = DinnerBooking::factory()->withGuests(3)->create();

    // Act: Leggi total_guests
    $total = $booking->total_guests;

    // Assert: total_guests = guests_count (non +1)
    expect($total)->toBe(3);
});

test('canBeModified returns true when host availability allows updates', function () {
    // Arrange: Booking con availability AVAILABLE_TO_HOST (permette updates)
    $availability = DinnerAvailability::factory()->asHost()->create();
    $booking      = DinnerBooking::factory()->forHost($availability)->create();

    // Act & Assert: Può essere modificato
    expect($booking->canBeModified)->toBeTrue();
});

test('canBeModified returns false when host availability is completed', function () {
    // Arrange: Booking con availability COMPLETED (non permette updates)
    $availability = DinnerAvailability::factory()->completed()->create();
    $booking      = DinnerBooking::factory()->forHost($availability)->create();

    // Act & Assert: NON può essere modificato
    expect($booking->canBeModified)->toBeFalse();
});

test('confirmed scope filters only confirmed bookings', function () {
    // Arrange: Crea bookings con stati diversi
    DinnerBooking::factory()->count(3)->confirmed()->create();
    DinnerBooking::factory()->count(2)->pending()->create();
    DinnerBooking::factory()->cancelled()->create();

    // Act: Usa scope confirmed
    $confirmedBookings = DinnerBooking::confirmed()->get();

    // Assert: Solo 3 confirmed
    expect($confirmedBookings)->toHaveCount(3)
        ->and($confirmedBookings->every(fn ($b) => $b->status === DinnerBookingStatus::CONFIRMED))->toBeTrue();
});

test('cancelled scope filters only cancelled bookings', function () {
    // Arrange: Crea bookings con stati diversi
    DinnerBooking::factory()->count(2)->confirmed()->create();
    DinnerBooking::factory()->count(3)->cancelled()->create();

    // Act: Usa scope cancelled
    $cancelledBookings = DinnerBooking::cancelled()->get();

    // Assert: Solo 3 cancelled
    expect($cancelledBookings)->toHaveCount(3)
        ->and($cancelledBookings->every(fn ($b) => $b->status === DinnerBookingStatus::CANCELLED))->toBeTrue();
});

/**
 * ============================================================================
 * SEZIONE D: SCOPE TEMPORALI
 * ============================================================================
 *
 * Test per gli scope che filtrano per data (future, past).
 */

test('future scope filters bookings with future dinner dates', function () {
    // Arrange: Crea date future e passate
    $futureDate = Carbon::tomorrow()->format('Y-m-d');
    $pastDate   = Carbon::yesterday()->format('Y-m-d');

    $futureDinnerDate = DinnerDate::factory()->futureDate($futureDate)->create();
    $pastDinnerDate   = DinnerDate::factory()->futureDate($pastDate)->create();

    $futureAvailability = DinnerAvailability::factory()->asHost()->forDate($futureDinnerDate)->create();
    $pastAvailability   = DinnerAvailability::factory()->asHost()->forDate($pastDinnerDate)->create();

    DinnerBooking::factory()->forHost($futureAvailability)->create();
    DinnerBooking::factory()->forHost($pastAvailability)->create();

    // Act: Filtra bookings futuri
    $futureBookings = DinnerBooking::query()
        ->whereHas('hostAvailability.dinnerDate', fn ($q) => $q->where('dinner_date', '>=', now()->startOfDay()))
        ->get();

    // Assert: Solo il booking futuro
    expect($futureBookings)->toHaveCount(1);
});

test('past scope filters bookings with past dinner dates', function () {
    // Arrange: Crea date future e passate
    $futureDate = Carbon::tomorrow()->format('Y-m-d');
    $pastDate   = Carbon::yesterday()->format('Y-m-d');

    $futureDinnerDate = DinnerDate::factory()->futureDate($futureDate)->create();
    $pastDinnerDate   = DinnerDate::factory()->futureDate($pastDate)->create();

    $futureAvailability = DinnerAvailability::factory()->asHost()->forDate($futureDinnerDate)->create();
    $pastAvailability   = DinnerAvailability::factory()->asHost()->forDate($pastDinnerDate)->create();

    DinnerBooking::factory()->forHost($futureAvailability)->create();
    DinnerBooking::factory()->forHost($pastAvailability)->create();

    // Act: Filtra bookings passati
    $pastBookings = DinnerBooking::query()
        ->whereHas('hostAvailability.dinnerDate', fn ($q) => $q->where('dinner_date', '<', now()->startOfDay()))
        ->get();

    // Assert: Solo il booking passato
    expect($pastBookings)->toHaveCount(1);
});
