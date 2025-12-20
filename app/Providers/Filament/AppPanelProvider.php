<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureProfileIsComplete;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // non usare /app perche' dedicata a reverb
        return $panel
            ->default()
            ->id('dinner')
            ->path('dinner')
            ->login()
            ->profile(\App\Filament\App\Auth\Pages\EditProfile::class)
            ->registration()
            ->emailVerification()
            ->renderHook('panels::body.end', fn (): string => \Illuminate\Support\Facades\Blade::render("@vite('resources/js/app.js')"))
            ->colors([
                'primary' => Color::Lime,
                'danger' => Color::Rose,
                'warning' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#f59e0b',
                    600 => '#d97706',
                    700 => '#b45309',
                    800 => '#92400e',
                    900 => '#78350f',
                    950 => '#451a03',
                ],
                'success' => [
                    50 => '#f0fdf4',
                    100 => '#dcfce7',
                    200 => '#bbf7d0',
                    300 => '#86efac',
                    400 => '#4ade80',
                    500 => '#22c55e',
                    600 => '#16a34a',
                    700 => '#15803d',
                    800 => '#166534',
                    900 => '#14532d',
                    950 => '#052e16',
                ],
                'info' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#f59e0b',
                    600 => '#d97706',
                    700 => '#b45309',
                    800 => '#92400e',
                    900 => '#78350f',
                    950 => '#451a03',
                ],
                'gray' => [
                    50 => '#f9f8f3',
                    100 => '#f1f0e5',
                    200 => '#e3e0ca',
                    300 => '#d0cba5',
                    400 => '#bab27e',
                    500 => '#a39a5e',
                    600 => '#5a541f',
                    700 => '#4d471a',
                    800 => '#403a17',
                    900 => '#363116',
                    950 => '#1c190b',
                ],
            ])
            ->darkMode()
            ->viteTheme('resources/css/filament/dinner/theme.css')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->renderHook(
                'panels::user-menu.before',
                function (): string {
                    /** @var User $user */
                    $user = Auth::user();

                    return $user?->is_admin
                        ? view('filament.panels.admin-link')->render()
                        : '';
                }
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([])
            ->authMiddleware([
                Authenticate::class,
                EnsureProfileIsComplete::class,
            ]);
    }
}
