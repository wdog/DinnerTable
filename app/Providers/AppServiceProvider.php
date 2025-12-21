<?php

namespace App\Providers;

use App\Models\DinnerBooking;
use Illuminate\Support\ServiceProvider;
use App\Observers\DinnerBookingObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // DinnerBooking::observe(DinnerBookingObserver::class);
    }
}
