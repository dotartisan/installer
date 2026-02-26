<?php

namespace Dotartisan\Installer;

use Dotartisan\Installer\Contracts\InstallServiceContract;
use Dotartisan\Installer\Contracts\UpdateServiceContract;
use Dotartisan\Installer\Services\DefaultUpdateService;
use Dotartisan\Installer\Services\Install\AbstractInstallService;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /**
         * Optional methods to load your package assets
         */
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'installer');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'installer');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('installer.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/installer'),
            ], 'views');

            // Publishing assets.
            $this->publishes([
                __DIR__ . '/../resources/dist' => public_path('vendor/installer'),
            ], 'assets');

            // Publishing the translation files.
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/installer'),
            ], 'lang');

            // Registering package commands.
            $this->commands([
                \Dotartisan\Installer\Console\InstallCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'installer');

        // Register the main class to use with the facade
        $this->app->singleton('installer', function () {
            return new Installer;
        });

        $this->app->singleton(UpdateServiceContract::class, function () {
            // Allow apps to override via config binding:
            $class = config('installer.update_service');

            if (is_string($class) && class_exists($class)) {
                return app($class);
            }

            return new DefaultUpdateService();
        });

        $this->app->singleton(InstallServiceContract::class, function () {
            $class = config('installer.install_service');

            if (class_exists($class)) {
                return app($class);
            }

            return new class extends AbstractInstallService {
                public function product(): string
                {
                    return 'unknown-item';
                }
                public function version(): string
                {
                    return '0.0.0';
                }
                public function createAdmin(array $admin): void {}
            };
        });
    }
}
