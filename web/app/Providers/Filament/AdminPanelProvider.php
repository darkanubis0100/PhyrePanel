<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Settings\Settings;
use App\Filament\Widgets\CustomersCount;
use App\Filament\Widgets\ServerDiskUsageStatistic;
use App\Filament\Widgets\ServerMemoryStatistic;
use App\Filament\Widgets\ServerMemoryStatisticCount;
use App\Filament\Widgets\Websites;
use App\Models\Module;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

         $panel->default()
            ->darkMode(true)
            ->id('admin')
            ->path('admin')
            ->login()
             ->unsavedChangesAlerts()
             ->globalSearch(true)
             ->databaseNotifications()
            ->font('Albert Sans')
            ->sidebarWidth('15rem')
          //  ->brandLogo(fn () => view('filament.admin.logo'))
            ->navigationGroups([
                'Hosting Services' => NavigationGroup::make()->label('Hosting Services'),
                'Server Management' => NavigationGroup::make()->label('Server Management'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                //  FilamentAuthenticationLogPlugin::make(),
                FilamentApexChartsPlugin::make(),
                FilamentSettingsPlugin::make()->pages([
                    Settings::class,
                ]),
            ])
         //   ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ServerDiskUsageStatistic::class,
                ServerMemoryStatistic::class,
                // ServerMemoryStatisticCount::class,
                CustomersCount::class,
                Websites::class,
                // Widgets\AccountWidget::class,
                //                Widgets\FilamentInfoWidget::class,
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
            ]);

        $defaultColor = Color::Yellow;
        $brandLogo = asset('images/phyre-logo.svg');

        if (!app()->runningInConsole()) {
            $isAppInstalled = file_exists(storage_path('installed'));
            if ($isAppInstalled) {
                if (setting('general.brand_logo_url')) {
                    $brandLogo = setting('general.brand_logo_url');
                }
                if (setting('general.brand_primary_color')) {
                    $defaultColor = Color::hex(setting('general.brand_primary_color'));
                }
                $findModules = Module::where('installed', 1)->get();
                if ($findModules->count() > 0) {
                    foreach ($findModules as $module) {
                        $modulePath = module_path($module->name, 'Filament/Clusters');
                        if (is_dir($modulePath)) {
                            $panel->discoverClusters(in: $modulePath, for: 'Modules\\' . $module->name . '\\Filament\\Clusters');
                        }
                    }
                }
                //            ->discoverClusters(in: module_path('Microweber', 'Filament/Clusters'), for: 'Modules\\Microweber\\Filament\\Clusters')
//            ->discoverClusters(in: module_path('LetsEncrypt', 'Filament/Clusters'), for: 'Modules\\LetsEncrypt\\Filament\\Clusters')
//            ->discoverClusters(in: module_path('Docker', 'Filament/Clusters'), for: 'Modules\\Docker\\Filament\\Clusters')

            }
        }

        $panel->brandLogo($brandLogo)
        ->brandLogoHeight('2.2rem')
        ->colors([
            'primary'=>$defaultColor,
        ]);

        return $panel;
    }
}
