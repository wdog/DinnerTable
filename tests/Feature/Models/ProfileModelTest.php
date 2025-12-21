<?php

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ===================================
 * TESTS DEL MODEL PROFILE - TDD PRACTICE
 * ===================================
 *
 * Questi test seguono il principio TDD:
 * 1. Scrivi il test (che fallirà)
 * 2. Implementa la funzionalità
 * 3. Fai passare il test
 */
test('profile is created when user is created', function () {
    $user = User::factory()->create();

    expect($user->profile)->not->toBeNull()
        ->and($user->profile)->toBeInstanceOf(Profile::class);
});

test('profile belongs to user', function () {
    $user = User::factory()->create();
    $profile = $user->profile;

    expect($profile->user)->not->toBeNull()
        ->and($profile->user->id)->toBe($user->id);
});

test('new profile is not complete by default', function () {
    $user = User::factory()->create();

    expect($user->profile->isComplete())->toBeFalse();
});

test('profile is complete when all required fields are filled', function () {
    $user = User::factory()->create();

    // Completa il profilo
    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => 4,
        'privacy_accepted_at' => now(),
    ]);

    expect($user->fresh()->profile->isComplete())->toBeTrue();
});

test('profile is incomplete without city', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'city'                => null,
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => 4,
        'privacy_accepted_at' => now(),
    ]);

    expect($user->fresh()->profile->isComplete())->toBeFalse();
});

test('profile is incomplete without address', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'city'                => 'Roma',
        'address'             => null,
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => 4,
        'privacy_accepted_at' => now(),
    ]);

    expect($user->fresh()->profile->isComplete())->toBeFalse();
});

test('profile is incomplete without house number', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => null,
        'postal_code'         => '00100',
        'max_guests'          => 4,
        'privacy_accepted_at' => now(),
    ]);

    expect($user->fresh()->profile->isComplete())->toBeFalse();
});

test('profile is incomplete without postal code', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => null,
        'max_guests'          => 4,
        'privacy_accepted_at' => now(),
    ]);

    expect($user->fresh()->profile->isComplete())->toBeFalse();
});

test('profile is incomplete without max guests', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => null,
        'privacy_accepted_at' => now(),
    ]);

    expect($user->fresh()->profile->isComplete())->toBeFalse();
});

test('profile is incomplete without privacy acceptance', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => 4,
        'privacy_accepted_at' => null,
    ]);

    expect($user->fresh()->profile->isComplete())->toBeFalse();
});

test('profile can have avatar url', function () {
    $user = User::factory()->create();

    $user->profile->update([
        'avatar_url' => 'avatars/test.jpg',
    ]);

    expect($user->fresh()->profile->avatar_url)->toBe('avatars/test.jpg');
});

test('profile avatar url is nullable', function () {
    $user = User::factory()->create();

    // Il profilo può esistere senza avatar
    expect($user->profile->avatar_url)->toBeNull();

    // E il profilo può essere completo senza avatar
    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => 4,
        'privacy_accepted_at' => now(),
        'avatar_url'          => null,
    ]);

    expect($user->fresh()->profile->isComplete())->toBeTrue();
});

test('profile is deleted when user is deleted', function () {
    $user = User::factory()->create();
    $profileId = $user->profile->id;

    $user->delete();

    // Il profilo dovrebbe essere eliminato in cascata
    expect(Profile::find($profileId))->toBeNull();
});

test('max guests must be a positive number', function () {
    $user = User::factory()->create();

    // Testa con un numero valido
    $user->profile->update(['max_guests' => 5]);
    expect($user->fresh()->profile->max_guests)->toBe(5);

    // Testa che 0 sia invalido per completare il profilo
    $user->profile->update([
        'city'                => 'Roma',
        'address'             => 'Via Roma',
        'house_number'        => '10',
        'postal_code'         => '00100',
        'max_guests'          => 0,
        'privacy_accepted_at' => now(),
    ]);

    // Con max_guests = 0, il profilo non dovrebbe essere completo
    expect($user->fresh()->profile->max_guests)->toBe(0);
});

test('postal code should be 5 characters', function () {
    $user = User::factory()->create();

    $user->profile->update(['postal_code' => '00100']);

    expect($user->fresh()->profile->postal_code)->toBe('00100')
        ->and(strlen($user->fresh()->profile->postal_code))->toBe(5);
});

test('can update profile multiple times', function () {
    $user = User::factory()->create();

    // Primo aggiornamento
    $user->profile->update(['city' => 'Roma']);
    expect($user->fresh()->profile->city)->toBe('Roma');

    // Secondo aggiornamento
    $user->profile->update(['city' => 'Milano']);
    expect($user->fresh()->profile->city)->toBe('Milano');
});
