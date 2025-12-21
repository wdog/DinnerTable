<?php

namespace App\Providers;

use App\Models\DinnerAvailability;
use App\Models\DinnerBooking;
use App\Observers\DinnerBookingObserver;
use App\Policies\DinnerBookingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        DinnerBooking::observe(DinnerBookingObserver::class);

        // Registra il gate custom 'book' per DinnerAvailability
        Gate::define('book', function ($user, DinnerAvailability $availability) {
            $policy = new DinnerBookingPolicy();
            return $policy->book($user, $availability);
        });
    }
}
