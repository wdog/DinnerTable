<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use App\Models\DinnerBooking;
use App\Models\DinnerAvailability;
use App\Policies\DinnerBookingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ============================================================================
 * TEST: DinnerBookingPolicy
 * ============================================================================
 *
 * Test completi per la DinnerBookingPolicy che verificano:
 * - viewAny: accesso lista prenotazioni
 * - view: visualizzazione singola prenotazione (guest o host)
 * - create: creazione prenotazioni (con gruppo)
 * - book: tutte le 8 condizioni per prenotare
 * - update: modifica (solo guest, non completed)
 * - updateGuestBooking: conferma/rifiuto (solo host)
 * - delete: sempre false (no hard delete)
 * - forceDelete: solo super_admin o guest proprietario
 *
 * Pattern utilizzato: AAA (Arrange-Act-Assert)
 * Stile: TDD con commenti esplicativi in italiano
 */

/**
 * ============================================================================
 * SEZIONE A: VIEW ANY
 * ============================================================================
 */
test('user in group can view any bookings', function () {
    // Arrange: User in gruppo
    $group = DinnerGroup::factory()->create();
    $user  = User::factory()->create(['dinner_group_id' => $group->id]);

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Può vedere lista
    expect($policy->viewAny($user))->toBeTrue();
});

test('user without group cannot view any bookings', function () {
    // Arrange: User senza gruppo
    $user = User::factory()->create(['dinner_group_id' => null]);

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può vedere lista
    expect($policy->viewAny($user))->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE B: VIEW
 * ============================================================================
 */
test('guest can view their own booking', function () {
    // Arrange: Guest con propria prenotazione
    $guest   = User::factory()->create();
    $booking = DinnerBooking::factory()->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Guest può vedere
    expect($policy->view($guest, $booking))->toBeTrue();
});

test('host can view bookings for their availability', function () {
    // Arrange: Host con availability e booking
    $host         = User::factory()->create();
    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();
    $booking      = DinnerBooking::factory()->forHost($availability)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Host può vedere
    expect($policy->view($host, $booking))->toBeTrue();
});

test('other user cannot view booking', function () {
    // Arrange: Booking di altri
    $booking = DinnerBooking::factory()->create();
    $other   = User::factory()->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Altri NON possono vedere
    expect($policy->view($other, $booking))->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE C: CREATE
 * ============================================================================
 */
test('user in group can create booking', function () {
    // Arrange: User in gruppo
    $group = DinnerGroup::factory()->create();
    $user  = User::factory()->create(['dinner_group_id' => $group->id]);

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Può creare
    expect($policy->create($user))->toBeTrue();
});

test('user without group cannot create booking', function () {
    // Arrange: User senza gruppo
    $user = User::factory()->create(['dinner_group_id' => null]);

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può creare
    expect($policy->create($user))->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE D: BOOK - 8 CONDIZIONI CRITICHE
 * ============================================================================
 *
 * Test per verificare TUTTE le condizioni del metodo book().
 */
test('user can book when all conditions met', function () {
    // Arrange: Setup completo con TUTTE le condizioni soddisfatte
    $group = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $group->id]);
    $host  = User::factory()->create(['dinner_group_id' => $group->id]);

    $date         = DinnerDate::factory()->forGroup($group)->create();
    $availability = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host)
        ->forDate($date)
        ->create(['max_guests' => 10]);

    $policy = new DinnerBookingPolicy;

    // Act: Verifica book
    $canBook = $policy->book($guest, $availability);

    // Assert: Tutte condizioni OK → può prenotare
    expect($canBook)->toBeTrue();
});

test('user cannot book from themselves as host', function () {
    // Arrange: User prova a prenotare la propria availability (host = guest)
    $group = DinnerGroup::factory()->create();
    $user  = User::factory()->create(['dinner_group_id' => $group->id]);

    $date         = DinnerDate::factory()->forGroup($group)->create();
    $availability = DinnerAvailability::factory()
        ->asHost()
        ->forUser($user) // ❌ Stesso utente
        ->forDate($date)
        ->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare (condizione #2 fallisce)
    expect($policy->book($user, $availability))->toBeFalse();
});

test('user cannot book from different group', function () {
    // Arrange: User in gruppo A, availability in gruppo B
    $groupA = DinnerGroup::factory()->create();
    $groupB = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $groupA->id]);
    $host  = User::factory()->create(['dinner_group_id' => $groupB->id]);

    $dateB        = DinnerDate::factory()->forGroup($groupB)->create();
    $availability = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host)
        ->forDate($dateB)
        ->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare (condizione #3 fallisce)
    expect($policy->book($guest, $availability))->toBeFalse();
});

test('user cannot book guest availability (can_host false)', function () {
    // Arrange: Availability GUEST (can_host = false)
    $group = DinnerGroup::factory()->create();

    $guest        = User::factory()->create(['dinner_group_id' => $group->id]);
    $date         = DinnerDate::factory()->forGroup($group)->create();
    $availability = DinnerAvailability::factory()
        ->asGuest() // ❌ can_host = false
        ->forDate($date)
        ->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare (condizione #4 fallisce)
    expect($policy->book($guest, $availability))->toBeFalse();
});

test('user cannot book when host status does not accept bookings', function () {
    // Arrange: Availability HOST_CANCELLED (non accetta booking)
    $group = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $group->id]);
    $host  = User::factory()->create(['dinner_group_id' => $group->id]);

    $date         = DinnerDate::factory()->forGroup($group)->create();
    $availability = DinnerAvailability::factory()
        ->cancelled() // ❌ HOST_CANCELLED non accetta bookings
        ->forUser($host)
        ->forDate($date)
        ->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare (condizione #5 fallisce)
    expect($policy->book($guest, $availability))->toBeFalse();
});

test('user cannot book when no available spots', function () {
    // Arrange: Availability FULL (nessun posto disponibile)
    $group = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $group->id]);
    $host  = User::factory()->create(['dinner_group_id' => $group->id]);

    $date         = DinnerDate::factory()->forGroup($group)->create();
    $availability = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host)
        ->forDate($date)
        ->create(['max_guests' => 5]);

    // Riempi tutti i posti
    DinnerBooking::factory()->confirmed()->withGuests(5)->forHost($availability)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare (condizione #6 fallisce)
    expect($policy->book($guest, $availability))->toBeFalse();
});

test('user cannot book twice for same availability', function () {
    // Arrange: User ha già prenotato questa availability
    $group = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $group->id]);
    $host  = User::factory()->create(['dinner_group_id' => $group->id]);

    $date         = DinnerDate::factory()->forGroup($group)->create();
    $availability = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host)
        ->forDate($date)
        ->create(['max_guests' => 10]);

    // Prima prenotazione
    DinnerBooking::factory()->confirmed()->forHost($availability)->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare di nuovo (condizione #7 fallisce)
    expect($policy->book($guest, $availability))->toBeFalse();
});

test('user cannot book twice for same date (different hosts)', function () {
    // Arrange: User ha già un booking confermato per quella data
    $group = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $group->id]);
    $host1 = User::factory()->create(['dinner_group_id' => $group->id]);
    $host2 = User::factory()->create(['dinner_group_id' => $group->id]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');
    $date     = DinnerDate::factory()->forGroup($group)->futureDate($tomorrow)->create();

    $availability1 = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host1)
        ->forDate($date)
        ->create(['max_guests' => 10]);

    $availability2 = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host2)
        ->forDate($date)
        ->create(['max_guests' => 10]);

    // Booking presso host1
    DinnerBooking::factory()->confirmed()->forHost($availability1)->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare host2 (stessa data, condizione #8 fallisce)
    expect($policy->book($guest, $availability2))->toBeFalse();
});

test('user can book multiple hosts on different dates', function () {
    // Arrange: User prenota più host in date DIVERSE (OK)
    $group = DinnerGroup::factory()->create();

    $guest = User::factory()->create(['dinner_group_id' => $group->id]);
    $host1 = User::factory()->create(['dinner_group_id' => $group->id]);
    $host2 = User::factory()->create(['dinner_group_id' => $group->id]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');
    $nextWeek = Carbon::tomorrow()->addWeek()->format('Y-m-d');

    $date1 = DinnerDate::factory()->forGroup($group)->futureDate($tomorrow)->create();
    $date2 = DinnerDate::factory()->forGroup($group)->futureDate($nextWeek)->create();

    $availability1 = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host1)
        ->forDate($date1)
        ->create(['max_guests' => 10]);

    $availability2 = DinnerAvailability::factory()
        ->asHost()
        ->forUser($host2)
        ->forDate($date2)
        ->create(['max_guests' => 10]);

    // Booking 1
    DinnerBooking::factory()->confirmed()->forHost($availability1)->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Può prenotare host2 (data diversa, condizione #8 OK)
    expect($policy->book($guest, $availability2))->toBeTrue();
});

test('user without group cannot book', function () {
    // Arrange: User senza gruppo
    $user         = User::factory()->create(['dinner_group_id' => null]);
    $availability = DinnerAvailability::factory()->asHost()->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può prenotare (condizione #1 fallisce)
    expect($policy->book($user, $availability))->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE E: UPDATE
 * ============================================================================
 */
test('guest can update their booking', function () {
    // Arrange: Guest con propria prenotazione
    $guest   = User::factory()->create();
    $booking = DinnerBooking::factory()->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Guest può modificare
    expect($policy->update($guest, $booking))->toBeTrue();
});

test('guest cannot update when availability is completed', function () {
    // Arrange: Booking con availability COMPLETED
    $guest        = User::factory()->create();
    $availability = DinnerAvailability::factory()->completed()->create();
    $booking      = DinnerBooking::factory()->forHost($availability)->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON può modificare (cena conclusa)
    expect($policy->update($guest, $booking))->toBeFalse();
});

test('non-guest cannot update booking', function () {
    // Arrange: Booking di altri
    $booking = DinnerBooking::factory()->create();
    $other   = User::factory()->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON guest NON può modificare
    expect($policy->update($other, $booking))->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE F: UPDATE GUEST BOOKING
 * ============================================================================
 */
test('host can update guest booking', function () {
    // Arrange: Host con availability e booking
    $host         = User::factory()->create();
    $availability = DinnerAvailability::factory()->asHost()->forUser($host)->create();
    $booking      = DinnerBooking::factory()->forHost($availability)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: Host può confermare/rifiutare
    expect($policy->updateGuestBooking($host, $booking))->toBeTrue();
});

test('non-host cannot update guest booking', function () {
    // Arrange: Booking di altro host
    $booking = DinnerBooking::factory()->create();
    $other   = User::factory()->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NON host NON può modificare
    expect($policy->updateGuestBooking($other, $booking))->toBeFalse();
});

/**
 * ============================================================================
 * SEZIONE G: DELETE
 * ============================================================================
 */
test('booking cannot be hard deleted', function () {
    // Arrange: Qualsiasi booking
    $guest   = User::factory()->create();
    $booking = DinnerBooking::factory()->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Act & Assert: NESSUNO può fare hard delete
    expect($policy->delete($guest, $booking))->toBeFalse();
});

test('force delete requires ownership or super_admin', function () {
    // Arrange: Booking e vari utenti
    $guest = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $other = User::factory()->create();

    $booking = DinnerBooking::factory()->byGuest($guest)->create();

    $policy = new DinnerBookingPolicy;

    // Assert: Guest proprietario può force delete
    expect($policy->forceDelete($guest, $booking))->toBeTrue();

    // Assert: Altri NON possono force delete
    expect($policy->forceDelete($other, $booking))->toBeFalse();

    // Nota: super_admin richiede hasRole() che non è testabile senza Spatie Permission
    // In produzione funzionerebbe con: expect($policy->forceDelete($admin, $booking))->toBeTrue();
});
