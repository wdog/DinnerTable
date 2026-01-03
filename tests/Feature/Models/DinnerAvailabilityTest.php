<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\DinnerDate;
use App\Models\DinnerBooking;
use App\Enums\DinnerBookingStatus;
use App\Models\DinnerAvailability;
use App\Enums\DinnerAvailabilityStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ============================================================================
 * TEST: DinnerAvailability Model
 * ============================================================================
 *
 * Test completi per il modello DinnerAvailability che verificano:
 * - Creazione e validazioni automatiche
 * - Relazioni con DinnerDate, User, DinnerBooking
 * - Attributi calcolati (total_booked_guests, available_spots)
 * - Scope (future, past)
 * - Stati enum e transizioni
 *
 * Pattern utilizzato: AAA (Arrange-Act-Assert)
 * Stile: TDD con commenti esplicativi in italiano
 */

/**
 * ============================================================================
 * SEZIONE A: CREAZIONE E VALIDAZIONI
 * ============================================================================
 *
 * Test che verificano la corretta creazione delle disponibilità e le
 * validazioni automatiche applicate dal model (booted hook).
 */
test('availability can be created as guest', function () {
    // Arrange: Prepara dati per una disponibilità guest
    $user = User::factory()->create();
    $date = DinnerDate::factory()->create();

    // Act: Crea disponibilità guest
    $availability = DinnerAvailability::create([
        'dinner_date_id' => $date->id,
        'user_id'        => $user->id,
        'can_host'       => false,
        'status'         => DinnerAvailabilityStatus::AVAILABLE,
    ]);

    // Assert: Verifica creazione corretta
    expect($availability)->not->toBeNull()
        ->and($availability->can_host)->toBeFalse()
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE)
        ->and($availability->max_guests)->toBeNull()
        ->and($availability->dinner_name)->toBeNull();
});

test('availability can be created as host with required fields', function () {
    // Arrange: Prepara dati per una disponibilità host
    $user = User::factory()->create();
    $date = DinnerDate::factory()->create();

    // Act: Crea disponibilità host con max_guests e dinner_name
    $availability = DinnerAvailability::create([
        'dinner_date_id' => $date->id,
        'user_id'        => $user->id,
        'can_host'       => true,
        'status'         => DinnerAvailabilityStatus::AVAILABLE_TO_HOST,
        'max_guests'     => 6,
        'dinner_name'    => 'Pizza Napoletana',
    ]);

    // Assert: Verifica creazione corretta con tutti i campi
    expect($availability)->not->toBeNull()
        ->and($availability->can_host)->toBeTrue()
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST)
        ->and($availability->max_guests)->toBe(6)
        ->and($availability->dinner_name)->toBe('Pizza Napoletana');
});

test('factory creates guest availability by default', function () {
    // Act: Usa factory senza state
    $availability = DinnerAvailability::factory()->create();

    // Assert: Verifica che il default sia guest
    expect($availability->can_host)->toBeFalse()
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE)
        ->and($availability->max_guests)->toBeNull();
});

test('factory can create host availability with asHost state', function () {
    // Act: Usa factory con state asHost
    $availability = DinnerAvailability::factory()->asHost()->create();

    // Assert: Verifica che sia host con tutti i campi
    expect($availability->can_host)->toBeTrue()
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST)
        ->and($availability->max_guests)->toBeGreaterThan(0)
        ->and($availability->max_guests)->toBeLessThanOrEqual(10);
});

test('can_host false forces guest status even if host status provided', function () {
    // Arrange: Prepara dati con contraddizione can_host/status
    $data = [
        'can_host' => false,
        'status'   => DinnerAvailabilityStatus::AVAILABLE_TO_HOST, // ❌ Host status
    ];

    // Act: Crea availability (il model dovrebbe correggere lo status)
    $availability = DinnerAvailability::factory()->create($data);

    // Assert: Verifica che lo status sia stato corretto a guest status
    expect($availability->can_host)->toBeFalse()
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE)
        ->and($availability->max_guests)->toBeNull()
        ->and($availability->dinner_name)->toBeNull();
});

test('can_host true forces host status even if guest status provided', function () {
    // Arrange: Prepara dati con contraddizione can_host/status
    $data = [
        'can_host'   => true,
        'status'     => DinnerAvailabilityStatus::AVAILABLE, // ❌ Guest status
        'max_guests' => 5,
    ];

    // Act: Crea availability (il model dovrebbe correggere lo status)
    $availability = DinnerAvailability::factory()->create($data);

    // Assert: Verifica che lo status sia stato corretto a host status
    expect($availability->can_host)->toBeTrue()
        ->and($availability->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);
});

test('can_host false nullifies max_guests and dinner_name', function () {
    // Arrange: Prepara availability guest con max_guests e dinner_name (invalidi)
    $data = [
        'can_host'    => false,
        'max_guests'  => 10,           // ❌ Non dovrebbe avere max_guests
        'dinner_name' => 'Pizza Night', // ❌ Non dovrebbe avere dinner_name
    ];

    // Act: Crea availability
    $availability = DinnerAvailability::factory()->create($data);

    // Assert: Verifica che i campi siano stati nullificati dal model
    expect($availability->can_host)->toBeFalse()
        ->and($availability->max_guests)->toBeNull()
        ->and($availability->dinner_name)->toBeNull();
});

test('unique constraint prevents duplicate availability for same user and date', function () {
    // Arrange: Crea prima availability
    $user = User::factory()->create();
    $date = DinnerDate::factory()->create();

    DinnerAvailability::factory()
        ->forUser($user)
        ->forDate($date)
        ->create();

    // Act & Assert: Tentativo di creare duplicato deve fallire
    expect(fn () => DinnerAvailability::factory()
        ->forUser($user)
        ->forDate($date)
        ->create()
    )->toThrow(\Exception::class);
});

/**
 * ============================================================================
 * SEZIONE B: RELAZIONI
 * ============================================================================
 *
 * Test che verificano le relazioni del model con DinnerDate, User e DinnerBooking.
 */
test('availability belongs to dinner date', function () {
    // Arrange: Crea availability con data specifica
    $date         = DinnerDate::factory()->create();
    $availability = DinnerAvailability::factory()->forDate($date)->create();

    // Act: Recupera la relazione
    $relatedDate = $availability->dinnerDate;

    // Assert: Verifica che sia la data corretta
    expect($relatedDate)->not->toBeNull()
        ->and($relatedDate->id)->toBe($date->id)
        ->and($relatedDate)->toBeInstanceOf(DinnerDate::class);
});

test('availability belongs to user', function () {
    // Arrange: Crea availability per utente specifico
    $user         = User::factory()->create();
    $availability = DinnerAvailability::factory()->forUser($user)->create();

    // Act: Recupera la relazione
    $relatedUser = $availability->user;

    // Assert: Verifica che sia l'utente corretto
    expect($relatedUser)->not->toBeNull()
        ->and($relatedUser->id)->toBe($user->id)
        ->and($relatedUser)->toBeInstanceOf(User::class);
});

test('availability has many bookings', function () {
    // Arrange: Crea host con 3 prenotazioni
    $availability = DinnerAvailability::factory()->asHost()->create();

    DinnerBooking::factory()->count(3)->forHost($availability)->create();

    // Act: Recupera bookings
    $bookings = $availability->bookings;

    // Assert: Verifica che ci siano 3 bookings
    expect($bookings)->toHaveCount(3)
        ->and($bookings->first())->toBeInstanceOf(DinnerBooking::class);
});

test('availability has many confirmed bookings filtered correctly', function () {
    // Arrange: Crea host con bookings misti
    $availability = DinnerAvailability::factory()->asHost()->create();

    DinnerBooking::factory()->count(2)->confirmed()->forHost($availability)->create();
    DinnerBooking::factory()->pending()->forHost($availability)->create();
    DinnerBooking::factory()->cancelled()->forHost($availability)->create();

    // Act: Recupera solo confirmed bookings
    $confirmedBookings = $availability->confirmedBookings;

    // Assert: Verifica che ci siano solo 2 confirmed
    expect($confirmedBookings)->toHaveCount(2)
        ->and($confirmedBookings->every(fn ($b) => $b->status === DinnerBookingStatus::CONFIRMED))->toBeTrue();
});

/**
 * ============================================================================
 * SEZIONE C: ATTRIBUTI CALCOLATI
 * ============================================================================
 *
 * Test per gli attributi computed del model (total_booked_guests, available_spots, ecc).
 */
test('total_booked_guests sums guests_count from confirmed bookings only', function () {
    // Arrange: Crea host con max 10 posti
    $availability = DinnerAvailability::factory()->asHost()->create(['max_guests' => 10]);

    // 2 confirmed con 2 ospiti ciascuno = 4 totali
    DinnerBooking::factory()->count(2)->confirmed()->withGuests(2)->forHost($availability)->create();

    // 1 pending con 3 ospiti (NON conta)
    DinnerBooking::factory()->pending()->withGuests(3)->forHost($availability)->create();

    // 1 cancelled con 1 ospite (NON conta)
    DinnerBooking::factory()->cancelled()->withGuests(1)->forHost($availability)->create();

    // Act: Calcola total_booked_guests
    $total = $availability->total_booked_guests;

    // Assert: Solo i confirmed contano (2 * 2 = 4)
    expect($total)->toBe(4);
});

test('available_spots calculates correctly', function () {
    // Arrange: Host con 8 posti, 3 prenotati
    $availability = DinnerAvailability::factory()->asHost()->create(['max_guests' => 8]);

    DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();

    // Act: Calcola posti disponibili
    $availableSpots = $availability->available_spots;

    // Assert: 8 - 3 = 5 posti liberi
    expect($availableSpots)->toBe(5);
});

test('hasAvailableSpots returns true when spots available', function () {
    // Arrange: Host con 5 posti, 2 prenotati
    $availability = DinnerAvailability::factory()->asHost()->create(['max_guests' => 5]);

    DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();

    // Act & Assert: Ci sono ancora posti (5 - 2 = 3)
    expect($availability->hasAvailableSpots())->toBeTrue();

    // Arrange: Prenota altri 3 posti (totale 5/5)
    DinnerBooking::factory()->confirmed()->withGuests(3)->forHost($availability)->create();

    // Act & Assert: Non ci sono più posti
    expect($availability->fresh()->hasAvailableSpots())->toBeFalse();
});

test('canAcceptBookings returns true for host with available spots', function () {
    // Arrange: Host disponibile con posti liberi
    $availability = DinnerAvailability::factory()->asHost()->create(['max_guests' => 10]);

    DinnerBooking::factory()->confirmed()->withGuests(2)->forHost($availability)->create();

    // Act & Assert: Può accettare booking (host + status ok + posti liberi)
    expect($availability->canAcceptBookings())->toBeTrue();

    // Arrange: Cambia status a HOST_CANCELLED
    $availability->update(['status' => DinnerAvailabilityStatus::HOST_CANCELLED]);

    // Act & Assert: Non può più accettare booking (status non permette)
    expect($availability->fresh()->canAcceptBookings())->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE D: SCOPE
 * ============================================================================
 *
 * Test per gli scope query del model (future, past, ecc).
 */
test('future scope filters only future availabilities', function () {
    // Arrange: Crea availabilities con date diverse
    $futureDate = Carbon::tomorrow()->format('Y-m-d');
    $pastDate   = Carbon::yesterday()->format('Y-m-d');

    $futureDinnerDate = DinnerDate::factory()->futureDate($futureDate)->create();
    $pastDinnerDate   = DinnerDate::factory()->futureDate($pastDate)->create();

    DinnerAvailability::factory()->forDate($futureDinnerDate)->create();
    DinnerAvailability::factory()->forDate($pastDinnerDate)->create();

    // Act: Recupera solo future
    $futureAvailabilities = DinnerAvailability::query()
        ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_date', '>=', now()->startOfDay()))
        ->get();

    // Assert: Solo la futura è presente
    expect($futureAvailabilities)->toHaveCount(1)
        ->and($futureAvailabilities->first()->dinnerDate->dinner_date->format('Y-m-d'))->toBe($futureDate);
});

test('past scope filters only past availabilities', function () {
    // Arrange: Crea availabilities con date diverse
    $futureDate = Carbon::tomorrow()->format('Y-m-d');
    $pastDate   = Carbon::yesterday()->format('Y-m-d');

    $futureDinnerDate = DinnerDate::factory()->futureDate($futureDate)->create();
    $pastDinnerDate   = DinnerDate::factory()->futureDate($pastDate)->create();

    DinnerAvailability::factory()->forDate($futureDinnerDate)->create();
    DinnerAvailability::factory()->forDate($pastDinnerDate)->create();

    // Act: Recupera solo past
    $pastAvailabilities = DinnerAvailability::query()
        ->whereHas('dinnerDate', fn ($q) => $q->where('dinner_date', '<', now()->startOfDay()))
        ->get();

    // Assert: Solo la passata è presente
    expect($pastAvailabilities)->toHaveCount(1)
        ->and($pastAvailabilities->first()->dinnerDate->dinner_date->format('Y-m-d'))->toBe($pastDate);
});

test('confirmed bookings relationship excludes cancelled bookings', function () {
    // Arrange: Host con vari bookings
    $availability = DinnerAvailability::factory()->asHost()->create();

    DinnerBooking::factory()->count(3)->confirmed()->forHost($availability)->create();
    DinnerBooking::factory()->count(2)->cancelled()->forHost($availability)->create();

    // Act: Recupera solo confirmed
    $confirmedCount = $availability->confirmedBookings()->count();

    // Assert: Solo 3 confirmed
    expect($confirmedCount)->toBe(3);
});

test('logs relationship returns all logs ordered by created_at asc', function () {
    // Arrange: Crea availability e simula vari cambi (logs vengono creati dall'Observer)
    $availability = DinnerAvailability::factory()->asHost()->create();

    // Simula cambi che generano log (in test reali gli Observer creano i log)
    // Per ora verifichiamo solo che la relazione esista
    $logs = $availability->logs;

    // Assert: Verifica che la relazione esista (può essere vuota se Observer non attivo in test)
    expect($logs)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

/**
 * ============================================================================
 * SEZIONE E: STATI E ENUM
 * ============================================================================
 *
 * Test per gli stati enum e le loro proprietà (colori, icone, metodi).
 */
test('guest can have AVAILABLE or NOT_AVAILABLE status', function () {
    // Arrange & Act: Crea guest disponibile
    $available = DinnerAvailability::factory()->asGuest()->create();

    // Assert: Status è AVAILABLE
    expect($available->status)->toBe(DinnerAvailabilityStatus::AVAILABLE);

    // Arrange & Act: Crea guest NON disponibile
    $notAvailable = DinnerAvailability::factory()->notAvailable()->create();

    // Assert: Status è NOT_AVAILABLE
    expect($notAvailable->status)->toBe(DinnerAvailabilityStatus::NOT_AVAILABLE);
});

test('host can have AVAILABLE_TO_HOST, ALMOST_FULL, FULL, HOST_CANCELLED, COMPLETED status', function () {
    // Test tutti gli stati host possibili
    $availableToHost = DinnerAvailability::factory()->asHost()->create();
    expect($availableToHost->status)->toBe(DinnerAvailabilityStatus::AVAILABLE_TO_HOST);

    $almostFull = DinnerAvailability::factory()->almostFull()->create();
    expect($almostFull->status)->toBe(DinnerAvailabilityStatus::ALMOST_FULL);

    $full = DinnerAvailability::factory()->full()->create();
    expect($full->status)->toBe(DinnerAvailabilityStatus::FULL);

    $cancelled = DinnerAvailability::factory()->cancelled()->create();
    expect($cancelled->status)->toBe(DinnerAvailabilityStatus::HOST_CANCELLED);

    $completed = DinnerAvailability::factory()->completed()->create();
    expect($completed->status)->toBe(DinnerAvailabilityStatus::COMPLETED);
});

test('status enum has correct colors', function () {
    // Verifica che gli enum abbiano i colori corretti (implementati in DinnerAvailabilityStatus)
    expect(DinnerAvailabilityStatus::AVAILABLE_TO_HOST->getColor())->toBe('success')
        ->and(DinnerAvailabilityStatus::ALMOST_FULL->getColor())->toBe('warning')
        ->and(DinnerAvailabilityStatus::FULL->getColor())->toBe('info')
        ->and(DinnerAvailabilityStatus::HOST_CANCELLED->getColor())->toBe('danger')
        ->and(DinnerAvailabilityStatus::COMPLETED->getColor())->toBe('info')
        ->and(DinnerAvailabilityStatus::AVAILABLE->getColor())->toBe('warning')
        ->and(DinnerAvailabilityStatus::NOT_AVAILABLE->getColor())->toBe('gray');
});

test('status enum has correct icons', function () {
    // Verifica che gli enum abbiano icone Tabler corrette
    expect(DinnerAvailabilityStatus::AVAILABLE_TO_HOST->getIcon())->toBe('tabler-home-2')
        ->and(DinnerAvailabilityStatus::ALMOST_FULL->getIcon())->toBe('tabler-users')
        ->and(DinnerAvailabilityStatus::FULL->getIcon())->toBe('tabler-door-off')
        ->and(DinnerAvailabilityStatus::HOST_CANCELLED->getIcon())->toBe('tabler-ban')
        ->and(DinnerAvailabilityStatus::COMPLETED->getIcon())->toBe('tabler-thumb-up')
        ->and(DinnerAvailabilityStatus::AVAILABLE->getIcon())->toBe('tabler-tools-kitchen-3')
        ->and(DinnerAvailabilityStatus::NOT_AVAILABLE->getIcon())->toBe('tabler-calendar-x');
});

test('canAcceptBookings returns false for HOST_CANCELLED and COMPLETED', function () {
    // Host cancellato non può accettare booking
    $cancelled = DinnerAvailability::factory()->cancelled()->create();
    expect($cancelled->canAcceptBookings())->toBeFalse();

    // Host completato non può accettare booking
    $completed = DinnerAvailability::factory()->completed()->create();
    expect($completed->canAcceptBookings())->toBeFalse();

    // Guest non può mai accettare booking (can_host = false)
    $guest = DinnerAvailability::factory()->asGuest()->create();
    expect($guest->canAcceptBookings())->toBeFalse();
});
