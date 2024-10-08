<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Auth\Register;
use App\Filament\Pages\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('user')
            ->login(Login::class)
            ->registration(Register::class)
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl()),
                MenuItem::make()
                    ->label('Vissza a kezőoldalra')
                    ->icon('iconoir-hot-air-balloon')
                    ->url('/'),
                //->openUrlInNewTab()
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                //Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
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
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications();
        //->databaseNotificationsPolling('30s');
    }
}
