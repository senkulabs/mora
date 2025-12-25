<?php

namespace SenkuLabs\Mora;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

abstract class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot() {}

    /**
     * Register all modules.
     */
    public function register() {}

    /**
     * Register all modules.
     */
    protected function registerModules()
    {
        $manifest = app(ModuleManifest::class);

        (new ProviderRepository($this->app, new Filesystem, $this->getCachedModulePath()))
            ->load($manifest->getProviders());

        $manifest->registerFiles();
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__.'/../config/config.php';

        $this->publishes([
            $configPath => config_path('modules.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    abstract protected function registerServices();

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [Contracts\RepositoryInterface::class, 'modules'];
    }

    protected function getCachedModulePath()
    {
        return Str::replaceLast('services.php', 'modules.php', $this->app->getCachedServicesPath());
    }
}
