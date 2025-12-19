<?php

namespace App\Providers\Filament;

use Filament\Panel;
use App\Models\User;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
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
            ->renderHook('panels::body.end', fn(): string => \Illuminate\Support\Facades\Blade::render("@vite('resources/js/app.js')"))
            ->colors([
                'primary' => Color::Purple,
                'gray' => Color::Slate,
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
                    $user = auth()->user();
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
