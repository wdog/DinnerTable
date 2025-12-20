<?php

use App\Models\DinnerDate;
use App\Models\DinnerGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dinner date can be created', function () {
    $group = DinnerGroup::factory()->create();
    $dinnerDate = DinnerDate::create([
        'dinner_group_id' => $group->id,
        'dinner_date' => '2025-12-25',
        'is_closed' => false,
    ]);
    expect($dinnerDate)->not->toBeNull()
        ->and($dinnerDate->dinner_group_id)->toBe($group->id);
});

test('dinner date unique constraint prevents duplicates', function () {
    $group = DinnerGroup::factory()->create();
    DinnerDate::create([
        'dinner_group_id' => $group->id,
        'dinner_date' => '2025-12-25',
    ]);
    expect(function () use ($group) {
        DinnerDate::create([
            'dinner_group_id' => $group->id,
            'dinner_date' => '2025-12-25',
        ]);
    })->toThrow(\Exception::class);
});
