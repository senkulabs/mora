<?php

namespace SenkuLabs\Mora;

use Illuminate\Support\ServiceProvider;
use SenkuLabs\Mora\Support\ModuleRegistry;
use SenkuLabs\Mora\Providers\ModularCommandsServiceProvider;

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

        // Bind ModuleRegistry singleton
        $this->app->singleton(ModuleRegistry::class, function ($app) {
            $modulesPath = $app['config']->get('modules.paths.modules', base_path('Modules'));
            $cachePath = $app->bootstrapPath('cache/modules.php');
            return new ModuleRegistry($modulesPath, $cachePath);
        });

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'modules');

        $this->app->register(ModularCommandsServiceProvider::class);
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
