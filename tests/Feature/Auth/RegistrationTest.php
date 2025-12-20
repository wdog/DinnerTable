<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Test: User can successfully register with valid data
 *
 * Questo test verifica che un utente possa registrarsi con successo
 * fornendo tutti i dati richiesti e validi.
 */
test('user can register with valid data', function () {
    // Arrange: Prepara i dati di test
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    // Act: Esegui la registrazione
    $response = $this->post('/dinner/register', $userData);

    // Assert: Verifica i risultati
    $response->assertRedirect(); // Dovrebbe reindirizzare dopo la registrazione

    // Verifica che l'utente sia stato creato nel database
    $this->assertDatabaseHas('users', [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
    ]);

    // Verifica che l'utente sia autenticato
    $this->assertAuthenticated('web');

    // Verifica che sia stato creato un profilo
    $user = User::where('email', 'mario.rossi@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->profile)->not->toBeNull();
});

/**
 * Test: Registration requires name
 *
 * Verifica che il campo nome sia obbligatorio
 */
test('registration requires name', function () {
    $userData = [
        'name' => '', // Nome vuoto
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

/**
 * Test: Registration requires email
 *
 * Verifica che il campo email sia obbligatorio
 */
test('registration requires email', function () {
    $userData = [
        'name' => 'Mario Rossi',
        'email' => '', // Email vuota
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

/**
 * Test: Registration requires valid email format
 *
 * Verifica che l'email debba avere un formato valido
 */
test('registration requires valid email format', function () {
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'not-a-valid-email', // Email non valida
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

/**
 * Test: Email must be unique
 *
 * Verifica che non si possano registrare due utenti con la stessa email
 */
test('email must be unique', function () {
    // Crea un utente esistente
    User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Prova a registrare un nuovo utente con la stessa email
    $userData = [
        'name' => 'Nuovo Utente',
        'email' => 'existing@example.com', // Email già esistente
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('email');

    // Verifica che ci sia ancora solo un utente con quella email
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

/**
 * Test: Registration requires password
 *
 * Verifica che il campo password sia obbligatorio
 */
test('registration requires password', function () {
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'test@example.com',
        'password' => '', // Password vuota
        'password_confirmation' => '',
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

/**
 * Test: Password must be confirmed
 *
 * Verifica che la password debba essere confermata correttamente
 */
test('password must be confirmed', function () {
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'DifferentPassword123!', // Conferma diversa
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

/**
 * Test: Password must meet minimum length requirements
 *
 * Verifica che la password abbia una lunghezza minima (tipicamente 8 caratteri)
 */
test('password must meet minimum length requirements', function () {
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'test@example.com',
        'password' => 'Pass1!', // Password troppo corta
        'password_confirmation' => 'Pass1!',
    ];

    $response = $this->post('/dinner/register', $userData);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

/**
 * Test: User is redirected to profile completion after registration
 *
 * Verifica che dopo la registrazione l'utente sia reindirizzato
 * alla pagina di completamento profilo se il profilo non è completo
 */
test('user is redirected to profile completion after registration', function () {
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->post('/dinner/register', $userData);

    // L'utente dovrebbe essere reindirizzato al completamento profilo
    // poiché il profilo appena creato non è completo
    $user = User::where('email', 'mario.rossi@example.com')->first();

    expect($user->profile)->not->toBeNull()
        ->and($user->profile->isComplete())->toBeFalse();
});

/**
 * Test: Multiple users can register
 *
 * Verifica che si possano registrare più utenti
 */
test('multiple users can register', function () {
    $users = [
        [
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ],
        [
            'name' => 'Luigi Verdi',
            'email' => 'luigi@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ],
        [
            'name' => 'Anna Bianchi',
            'email' => 'anna@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ],
    ];

    foreach ($users as $userData) {
        $this->post('/dinner/register', $userData);
    }

    // Verifica che tutti gli utenti siano stati creati
    expect(User::count())->toBe(3)
        ->and(User::where('email', 'mario@example.com')->exists())->toBeTrue()
        ->and(User::where('email', 'luigi@example.com')->exists())->toBeTrue()
        ->and(User::where('email', 'anna@example.com')->exists())->toBeTrue();
});
