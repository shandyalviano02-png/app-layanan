<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('SAPA SOSIAL — Dinsos Kab. Blitar')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->profile()
            ->databaseNotifications()
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('Layanan 1 — SK DTSEN'),
                \Filament\Navigation\NavigationGroup::make('Layanan 2 — Reaktivasi PBI-JK'),
                \Filament\Navigation\NavigationGroup::make('Layanan 3 — Rehabilitasi Sosial'),
                \Filament\Navigation\NavigationGroup::make('Layanan 4 — Layanan Lainnya'),
                \Filament\Navigation\NavigationGroup::make('Layanan 5 — Pengaduan Sosial'),
                \Filament\Navigation\NavigationGroup::make('Layanan 6 — Informasi'),
                \Filament\Navigation\NavigationGroup::make('Master Data'),
                \Filament\Navigation\NavigationGroup::make('Laporan'),
                \Filament\Navigation\NavigationGroup::make('Pengaturan Sistem'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
