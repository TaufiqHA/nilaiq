<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Resources\ClassesResource;
use App\Filament\Resources\ExtracurricularsResource;
use App\Filament\Resources\GuruMataPelajaranResource;
use App\Filament\Resources\SchoolsResource;
use App\Filament\Resources\StudentsResource;
use App\Filament\Resources\SubjectsResource;
use App\Filament\Resources\TeachersResource;
use App\Filament\Resources\WaliKelasResource;
use Filament\Enums\ThemeMode;
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
            ->default()
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()
            ->defaultThemeMode(ThemeMode::Light)
            ->brandName('NilaiQ')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->databaseNotifications()
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->spa()
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

                ])->groups([
                    NavigationGroup::make('Manajemen Sekolah')
                        ->items([
                            ...SchoolsResource::getNavigationItems(),
                            ...ClassesResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Manajemen Mata Pelajaran')
                        ->items([
                            ...SubjectsResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Manajemen Guru')
                        ->items([
                            ...TeachersResource::getNavigationItems(),
                            // ...WaliKelasResource::getNavigationItems(),
                            ...GuruMataPelajaranResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Manajemen Siswa')
                        ->items([
                            ...StudentsResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Manajemen Ekstrakurikuler')
                        ->items([
                            ...ExtracurricularsResource::getNavigationItems(),
                        ]),
                    ]);
            });
    }

}
