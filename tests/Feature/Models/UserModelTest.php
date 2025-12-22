<?php

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ===================================
 * TESTS DEL MODEL USER - ESEMPI TDD
 * ===================================
 *
 * Questi test mostrano come testare i model direttamente
 * senza dover gestire HTTP requests o Livewire components
 */
test('user can be created', function () {
    // Arrange: Prepara i dati
    $userData = [
        'name'     => 'Mario Rossi',
        'email'    => 'mario@example.com',
        'password' => bcrypt('password'),
    ];

    // Act: Crea l'utente
    $user = User::create($userData);

    // Assert: Verifica che sia stato creato
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Mario Rossi')
        ->and($user->email)->toBe('mario@example.com')
        ->and($user->id)->not->toBeNull();
});

test('user factory creates user with profile', function () {
    // Act: Crea utente con factory
    $user = User::factory()->create();

    // Assert: Verifica che utente e profilo esistano
    expect($user)->not->toBeNull()
        ->and($user->profile)->not->toBeNull()
        ->and($user->profile)->toBeInstanceOf(Profile::class);
});

test('user has profile relationship', function () {
    $user = User::factory()->create();

    // Verifica che la relazione profile esista
    expect($user->profile())->not->toBeNull()
        ->and($user->profile)->toBeInstanceOf(Profile::class);
});

test('user has dinner group relationship', function () {
    $user = User::factory()->create();

    // Un nuovo utente non dovrebbe avere un gruppo
    expect($user->dinnerGroup)->toBeNull()
        ->and($user->dinner_group_id)->toBeNull();
});

test('multiple users can be created', function () {
    // Crea 5 utenti
    User::factory()->count(5)->create();

    // Verifica che esistano 5 utenti
    expect(User::count())->toBe(5);
});

test('user email must be unique', function () {
    // Crea primo utente
    User::factory()->create(['email' => 'test@example.com']);

    // Prova a creare secondo utente con stessa email
    // Questo dovrebbe lanciare un'eccezione
    expect(fn () => User::factory()->create(['email' => 'test@example.com']))
        ->toThrow(\Exception::class);
});

test('user has name accessor', function () {
    $user = User::factory()->create([
        'name' => 'Mario Rossi',
    ]);

    expect($user->name)->toBe('Mario Rossi');
});

test('user password is hashed', function () {
    $plainPassword = 'my-secret-password';

    $user = User::create([
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => bcrypt($plainPassword),
    ]);

    // La password salvata non dovrebbe essere uguale a quella in chiaro
    expect($user->password)->not->toBe($plainPassword)
        // Ma dovrebbe essere verificabile con Hash::check
        ->and(\Illuminate\Support\Facades\Hash::check($plainPassword, $user->password))->toBeTrue();
});

test('user can have admin flag', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['is_admin' => false]);

    expect($admin->is_admin)->toBeTrue()
        ->and($user->is_admin)->toBeFalse();
});

test('user with incomplete profile cannot access dashboard', function () {
    $user = User::factory()->create();

    // Il profilo appena creato dovrebbe essere incompleto
    expect($user->profile->isComplete())->toBeFalse();
});

test('user timestamps are set automatically', function () {
    $user = User::factory()->create();

    expect($user->created_at)->not->toBeNull()
        ->and($user->updated_at)->not->toBeNull()
        ->and($user->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('can find user by email', function () {
    $createdUser = User::factory()->create([
        'email' => 'findme@example.com',
    ]);

    $foundUser = User::where('email', 'findme@example.com')->first();

    expect($foundUser)->not->toBeNull()
        ->and($foundUser->id)->toBe($createdUser->id);
});

test('can update user name', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $user->update(['name' => 'New Name']);

    expect($user->fresh()->name)->toBe('New Name');
});

test('can delete user', function () {
    $user   = User::factory()->create();
    $userId = $user->id;

    $user->delete();

    expect(User::find($userId))->toBeNull();
});
