<?php

namespace App\Providers\Filament;

use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;
use App\Filament\WaliKelas\Resources\StudentsResource;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\teachers;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class WaliKelasPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('waliKelas')
            ->path('waliKelas')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()
            ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/WaliKelas/Resources'), for: 'App\\Filament\\WaliKelas\\Resources')
            ->discoverPages(in: app_path('Filament/WaliKelas/Pages'), for: 'App\\Filament\\WaliKelas\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/WaliKelas/Widgets'), for: 'App\\Filament\\WaliKelas\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('waliKelas')
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render("@vite('resources/js/app.js')"),
            )
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make('Dashboard')
                        ->icon('heroicon-o-home')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                        ->url(fn (): string => Dashboard::getUrl()),
                ])
                ->groups([
                    NavigationGroup::make('Siswa')
                    ->items([
                        ...StudentsResource::getNavigationItems(),
                    ]),
                    NavigationGroup::make('Absensi')
                    ->items([
                        ...ClassAttendanceSessionsResource::getNavigationItems(),
                    ]),
                ]);
            });
    }

}
