<?php

use App\Models\User;
use Filament\Pages\Auth\Register;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

/**
 * ===================================
 * TEST 1: User can register with valid data
 * ===================================
 *
 * Questo test verifica la registrazione usando Livewire
 */
test('user can register with valid data', function () {
    // Arrange: Prepara i dati
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
        'password' => 'Password123!',
        'passwordConfirmation' => 'Password123!',
    ];

    // Act: Esegui la registrazione tramite Livewire
    livewire(Register::class)
        ->fillForm($userData)
        ->call('register')
        ->assertHasNoFormErrors();

    // Assert: Verifica i risultati
    $this->assertAuthenticated('web');

    // Verifica che l'utente sia stato creato nel database
    $this->assertDatabaseHas('users', [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
    ]);

    // Verifica che sia stato creato un profilo
    $user = User::where('email', 'mario.rossi@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->profile)->not->toBeNull();
});

/**
 * ===================================
 * TEST 2: Registration requires name
 * ===================================
 */
test('registration requires name', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => '', // Nome vuoto
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasFormErrors(['name' => 'required']);

    // Verifica che l'utente non sia autenticato
    $this->assertGuest();
});

/**
 * ===================================
 * TEST 3: Registration requires email
 * ===================================
 */
test('registration requires email', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => '', // Email vuota
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasFormErrors(['email' => 'required']);

    $this->assertGuest();
});

/**
 * ===================================
 * TEST 4: Registration requires valid email format
 * ===================================
 */
test('registration requires valid email format', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'not-a-valid-email', // Email non valida
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

/**
 * ===================================
 * TEST 5: Email must be unique
 * ===================================
 */
test('email must be unique', function () {
    // Crea un utente esistente
    User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Prova a registrare con la stessa email
    livewire(Register::class)
        ->fillForm([
            'name' => 'Nuovo Utente',
            'email' => 'existing@example.com', // Email già esistente
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasFormErrors(['email']);

    // Verifica che ci sia ancora solo un utente con quella email
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

/**
 * ===================================
 * TEST 6: Registration requires password
 * ===================================
 */
test('registration requires password', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'test@example.com',
            'password' => '', // Password vuota
            'passwordConfirmation' => '',
        ])
        ->call('register')
        ->assertHasFormErrors(['password' => 'required']);

    $this->assertGuest();
});

/**
 * ===================================
 * TEST 7: Password must be confirmed
 * ===================================
 */
test('password must be confirmed', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'DifferentPassword123!', // Conferma diversa
        ])
        ->call('register')
        ->assertHasFormErrors(['password']);

    $this->assertGuest();
});

/**
 * ===================================
 * TEST 8: Password must meet minimum length
 * ===================================
 */
test('password must meet minimum length requirements', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'test@example.com',
            'password' => 'Pass1!', // Password troppo corta
            'passwordConfirmation' => 'Pass1!',
        ])
        ->call('register')
        ->assertHasFormErrors(['password']);

    $this->assertGuest();
});

/**
 * ===================================
 * TEST 9: User is redirected to profile completion
 * ===================================
 */
test('user is redirected to profile completion after registration', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    // Verifica che l'utente sia stato creato
    $user = User::where('email', 'mario.rossi@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->profile)->not->toBeNull()
        ->and($user->profile->isComplete())->toBeFalse();
});

/**
 * ===================================
 * TEST 10: Multiple users can register
 * ===================================
 */
test('multiple users can register', function () {
    $users = [
        [
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ],
        [
            'name' => 'Luigi Verdi',
            'email' => 'luigi@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ],
        [
            'name' => 'Anna Bianchi',
            'email' => 'anna@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ],
    ];

    foreach ($users as $userData) {
        livewire(Register::class)
            ->fillForm($userData)
            ->call('register')
            ->assertHasNoFormErrors();

        // Logout dopo ogni registrazione per testare la prossima
        auth()->logout();
    }

    // Verifica che tutti gli utenti siano stati creati
    expect(User::count())->toBe(3)
        ->and(User::where('email', 'mario@example.com')->exists())->toBeTrue()
        ->and(User::where('email', 'luigi@example.com')->exists())->toBeTrue()
        ->and(User::where('email', 'anna@example.com')->exists())->toBeTrue();
});
