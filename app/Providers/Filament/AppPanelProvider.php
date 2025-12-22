<?php

namespace App\Providers\Filament;

use Filament\Panel;
use App\Models\User;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\EnsureProfileIsComplete;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

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
            ->sidebarWidth('15rem')
            ->maxContentWidth(Width::Full)
            ->colors([
                /*
    |--------------------------------------------------------------------------
    | Primary – Lime (brand)
    |--------------------------------------------------------------------------
    */
                'primary' => [
                    50  => '#F7FEE7',
                    100 => '#ECFCCB',
                    200 => '#D9F99D',
                    300 => '#BEF264',
                    400 => '#A3E635',
                    500 => '#84CC16', // main brand
                    600 => '#65A30D',
                    700 => '#4D7C0F',
                    800 => '#3F6212',
                    900 => '#365314',
                    950 => '#1A2E05',
                ],

                /*
    |--------------------------------------------------------------------------
    | Secondary – Olive (warm accent)
    |--------------------------------------------------------------------------
    */
                'secondary' => [
                    50  => '#FAFAF5',
                    100 => '#F3F4E6',
                    200 => '#E5E7C9',
                    300 => '#D4D8A8',
                    400 => '#BFC684',
                    500 => '#A3AD5F',
                    600 => '#7F8A3E',
                    700 => '#626B30',
                    800 => '#4D5528',
                    900 => '#404622',
                    950 => '#22250F',
                ],

                /*
    |--------------------------------------------------------------------------
    | Success – Emerald (clean & positive)
    |--------------------------------------------------------------------------
    */
                'success' => [
                    50  => '#ECFDF5',
                    100 => '#D1FAE5',
                    200 => '#A7F3D0',
                    300 => '#6EE7B7',
                    400 => '#34D399',
                    500 => '#10B981',
                    600 => '#059669',
                    700 => '#047857',
                    800 => '#065F46',
                    900 => '#064E3B',
                    950 => '#022C22',
                ],

                /*
    |--------------------------------------------------------------------------
    | Info – Sky (neutral contrast)
    |--------------------------------------------------------------------------
    */
                'info' => [
                    50  => '#F0F9FF',
                    100 => '#E0F2FE',
                    200 => '#BAE6FD',
                    300 => '#7DD3FC',
                    400 => '#38BDF8',
                    500 => '#0EA5E9',
                    600 => '#0284C7',
                    700 => '#0369A1',
                    800 => '#075985',
                    900 => '#0C4A6E',
                    950 => '#082F49',
                ],

                /*
    |--------------------------------------------------------------------------
    | Warning – Amber (warm alert)
    |--------------------------------------------------------------------------
    */
                'warning' => [
                    50  => '#FFFBEB',
                    100 => '#FEF3C7',
                    200 => '#FDE68A',
                    300 => '#FCD34D',
                    400 => '#FBBF24',
                    500 => '#F59E0B',
                    600 => '#D97706',
                    700 => '#B45309',
                    800 => '#92400E',
                    900 => '#78350F',
                    950 => '#451A03',
                ],

                /*
    |--------------------------------------------------------------------------
    | Danger – Rose (less aggressive than pure red)
    |--------------------------------------------------------------------------
    */
                'danger' => [
                    50  => '#FFF1F2',
                    100 => '#FFE4E6',
                    200 => '#FECDD3',
                    300 => '#FDA4AF',
                    400 => '#FB7185',
                    500 => '#F43F5E',
                    600 => '#E11D48',
                    700 => '#BE123C',
                    800 => '#9F1239',
                    900 => '#881337',
                    950 => '#4C0519',
                ],

                /*
    |--------------------------------------------------------------------------
    | Gray – Cream-based (light) → Deep Slate (dark)
    |--------------------------------------------------------------------------
    */
                'gray' => [
                    50  => '#F7F3E8', // darker cream background
                    100 => '#FAF7EF',
                    200 => '#F1EBDD',
                    300 => '#E4DCCB',
                    400 => '#CFC5AE',
                    500 => '#A8A091',
                    600 => '#7E7769',
                    700 => '#5F594F',
                    800 => '#3F3B35',
                    900 => '#03242b', // secion and box in dark
                    950 => '#062d35', // header colors in light mode - panel background in dark
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
