<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;

/**
 * Pagina di registrazione personalizzata per il panel app.
 *
 * Estende la pagina di registrazione base di Filament aggiungendo
 * stili responsive e link personalizzati.
 */
class Register extends BaseRegister
{
    protected string $view = 'filament.app.pages.auth.register';
}
