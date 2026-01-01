<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Enums\Width;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Blade;
use Filament\Http\Middleware\Authenticate;
use App\Filament\App\Auth\Pages\EditProfile;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class)
            ->brandName('DinnerTable Admin')
            ->brandLogo(asset('images/logo-small.png'))
            ->brandLogoHeight('5rem')
            ->favicon(asset('images/favicon.ico'))
            ->font('Roboto Condensed')
            ->unsavedChangesAlerts()
            ->renderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"))
            ->sidebarWidth('15rem')
            ->maxContentWidth(Width::Full)
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Amber,
                'gray'    => Color::Slate,
            ])
            ->darkMode(true)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->renderHook(
                'panels::user-menu.before',
                fn (): string => view('filament.panels.app-link')->render()
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
            ]);
    }
}
