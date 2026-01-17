<?php

namespace App\Providers\Filament;

use App\Filament\Auth\RegisterMentor;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\EditSiteSettings;
use App\Filament\Pages\MyModernProfile;
use App\Filament\Pages\RevenueReport;
use App\Livewire\MyCustomComponent;
use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Auth\Register;
use Filament\Support\Colors\Color;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\Livewire\UpdatePassword;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $setting = Setting::first();
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->pages([
                // ... halaman lain
            ])
            ->login()
            ->registration(RegisterMentor::class)
            ->brandName($setting?->site_name ?: 'GoEdu Admin')  // Tetap pasang untuk keperluan metadata/alt-text
            ->brandLogo(function () use ($setting) {
                // 1. Tentukan URL Logo
                $logoUrl = $setting?->logo
                    ? asset('storage/' . $setting->logo)
                    : asset('images/logo-default.svg');

                // 2. Tentukan Nama Site
                $siteName = $setting?->site_name ?: 'GoEdu Admin';

                // 3. Kembalikan HTML yang menggabungkan Gambar dan Teks
                return view('filament.components.brand-logo', [
                    'logoUrl' => $logoUrl,
                    'siteName' => $siteName,
                ]);
            })
            ->topNavigation()
            // ->sidebarCollapsibleOnDesktop()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::hex('#20C896'),
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                EditSiteSettings::class,
                Dashboard::class,  // ← ganti default dashboard
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('Profil Saya')
                    ->url(fn(): string => MyModernProfile::getUrl())
                    ->icon('heroicon-o-user'),
                'logout' => \Filament\Navigation\MenuItem::make()
                    ->label(fn(): string => \Filament\Facades\Filament::auth()->check() ? 'Keluar' : 'Masuk')
                    ->url(fn(): string => \Filament\Facades\Filament::auth()->check()
                        ? \Filament\Facades\Filament::getLogoutUrl()  // Menggunakan getLogoutUrl()
                        : \Filament\Facades\Filament::getLoginUrl()  // Menggunakan getLoginUrl()
                    )
                    ->icon('heroicon-o-arrow-left-on-rectangle'),
            ])
            ->plugin(
                BreezyCore::make()
                    ->myProfileComponents([])
                    ->myProfile(
                        hasAvatars: true,
                        //  enableAccountDeletion: true,
                    )
            );
    }
}
