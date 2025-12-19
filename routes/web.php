<?php

use Illuminate\Support\Facades\Route;

// Public home page
Route::get('/', function () {
    return view('home');
})->name('home');

// Filament handles both panel routings:
// - Friend Panel: /app (login, registration, user dashboard)
// - Admin Panel: /admin (admin only)
