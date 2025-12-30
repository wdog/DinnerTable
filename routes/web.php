<?php

use App\Models\AppReview;
use Illuminate\Support\Facades\Route;

// Public home page
Route::get('/', function () {
    $reviews = AppReview::with(['user.dinnerGroup'])
        ->where('rating', '>=', 4)
        ->whereNotNull('comment')
        ->latest()
        ->take(4)
        ->get();

    return view('home', compact('reviews'));
})->name('home');

// Filament handles both panel routings:
// - Friend Panel: /app (login, registration, user dashboard)
// - Admin Panel: /admin (admin only)

Route::get('test', fn () => view('landing'));
