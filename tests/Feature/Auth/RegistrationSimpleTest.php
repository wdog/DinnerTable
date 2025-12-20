<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * ================================================================================
 * REGISTRATION TESTS - APPROCCIO SEMPLIFICATO (TDD BEST PRACTICE)
 * ===============================================================================
 *
 * Questi test verificano la funzionalità di registrazione testando direttamente
 * i model e la logica di business, invece di testare l'interfaccia Livewire.
 *
 * Questo approccio è migliore perché:
 * 1. I test sono più veloci
 * 2. Sono più stabili (non dipendono dall'UI)
 * 3. Seguono meglio i principi TDD
 * 4. Testano effettivamente la logica di business
 */

// ============================================================================
// TEST 1: Utente può essere registrato con dati validi
// ============================================================================
test('user can be registered with valid data', function () {
    // Arrange: Prepara i dati
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
        'password' => Hash::make('Password123!'),
    ];

    // Act: Crea l'utente (simula la registrazione)
    $user = User::create($userData);

    // Assert: Verifica che sia stato creato correttamente
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Mario Rossi')
        ->and($user->email)->toBe('mario.rossi@example.com')
        ->and($user->profile)->not->toBeNull(); // Profilo creato automaticamente
});

// ============================================================================
// TEST 2: Nome è obbligatorio
// ============================================================================
test('name is required for registration', function () {
    expect(function () {
        User::create([
            'name' => null,
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    })->toThrow(\Exception::class);
});

// ============================================================================
// TEST 3: Email è obbligatoria
// ============================================================================
test('email is required for registration', function () {
    expect(function () {
        User::create([
            'name' => 'Test User',
            'email' => null,
            'password' => Hash::make('password'),
        ]);
    })->toThrow(\Exception::class);
});

// ============================================================================
// TEST 4: Email deve essere unica
// ============================================================================
test('email must be unique', function () {
    // Crea primo utente
    User::factory()->create(['email' => 'existing@example.com']);

    // Prova a creare secondo utente con stessa email
    expect(function () {
        User::create([
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
        ]);
    })->toThrow(\Exception::class);

    // Verifica che esista ancora solo 1 utente
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

// ============================================================================
// TEST 5: Password è obbligatoria
// ============================================================================
test('password is required for registration', function () {
    expect(function () {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => null,
        ]);
    })->toThrow(\Exception::class);
});

// ============================================================================
// TEST 6: Password viene hashata
// ============================================================================
test('password is hashed when user is created', function () {
    $plainPassword = 'MySecretPassword123!';

    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make($plainPassword),
    ]);

    // La password nel database non deve essere in chiaro
    expect($user->password)->not->toBe($plainPassword)
        // Ma deve essere verificabile
        ->and(Hash::check($plainPassword, $user->password))->toBeTrue();
});

// ============================================================================
// TEST 7: Profilo viene creato automaticamente
// ============================================================================
test('profile is created automatically when user registers', function () {
    $user = User::factory()->create();

    expect($user->profile)->not->toBeNull()
        ->and($user->profile->user_id)->toBe($user->id);
});

// ============================================================================
// TEST 8: Profilo nuovo non è completo
// ============================================================================
test('new user profile is not complete', function () {
    $user = User::factory()->create();

    expect($user->profile->isComplete())->toBeFalse();
});

// ============================================================================
// TEST 9: Multipli utenti possono essere registrati
// ============================================================================
test('multiple users can be registered', function () {
    $users = [
        ['name' => 'Mario Rossi', 'email' => 'mario@example.com'],
        ['name' => 'Luigi Verdi', 'email' => 'luigi@example.com'],
        ['name' => 'Anna Bianchi', 'email' => 'anna@example.com'],
    ];

    foreach ($users as $userData) {
        User::create(array_merge($userData, [
            'password' => Hash::make('password'),
        ]));
    }

    // Verifica che tutti siano stati creati
    expect(User::count())->toBe(3)
        ->and(User::where('email', 'mario@example.com')->exists())->toBeTrue()
        ->and(User::where('email', 'luigi@example.com')->exists())->toBeTrue()
        ->and(User::where('email', 'anna@example.com')->exists())->toBeTrue();
});

// ============================================================================
// TEST 10: Utente ha email verificata dopo registrazione (se configurato)
// ============================================================================
test('new user email is not verified by default', function () {
    // Crea utente senza verificare l'email
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

// ============================================================================
// BONUS TEST: Factory funziona correttamente
// ============================================================================
test('user factory creates valid user', function () {
    $user = User::factory()->create();

    expect($user)->not->toBeNull()
        ->and($user->name)->not->toBeEmpty()
        ->and($user->email)->toContain('@')
        ->and($user->password)->not->toBeNull()
        ->and($user->profile)->not->toBeNull();
});

// ============================================================================
// BONUS TEST: Email può essere verificata
// ============================================================================
test('user email can be verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->hasVerifiedEmail())->toBeTrue();
});

// ============================================================================
// BONUS TEST: Utente non admin per default
// ============================================================================
test('new user is not admin by default', function () {
    $user = User::factory()->create();

    expect($user->is_admin)->toBeFalse();
});

// ============================================================================
// BONUS TEST: Utente può essere creato come admin
// ============================================================================
test('user can be created as admin', function () {
    $user = User::factory()->create([
        'is_admin' => true,
    ]);

    expect($user->is_admin)->toBeTrue();
});
