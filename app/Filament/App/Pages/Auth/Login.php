<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

/**
 * Pagina di login personalizzata per il panel app.
 *
 * Estende la pagina di login base di Filament aggiungendo
 * stili responsive e link personalizzati.
 */
class Login extends BaseLogin
{
    protected string $view = 'filament.app.pages.auth.login';
}
