<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule: Completa automaticamente le disponibilità scadute.
 *
 * Eseguito ogni giorno alle 02:00 per impostare lo stato COMPLETED
 * per tutte le disponibilità il cui giorno della cena è passato.
 */
Schedule::command('availabilities:complete-expired')
    ->dailyAt('02:00')
    ->timezone('Europe/Rome')
    ->name('Complete Expired Availabilities')
    ->description('Completa le disponibilità il cui giorno della cena è passato');
