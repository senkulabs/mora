<?php

namespace SenkuLabs\Mora;

use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton('modules', function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new ModuleRepository($app, $path);
        });

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'modules');

        $this->app->register(Providers\ModularCommandsServiceProvider::class);
    }

    /**
     * Bootstrap the package.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('modules.php'),
        ], 'config');
    }
}
