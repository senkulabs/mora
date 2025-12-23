<?php

namespace SenkuLabs\Mora\Laravel;

use Illuminate\Support\Facades\Facade;
use SenkuLabs\Mora\Module;

class LaravelModule extends Module
{
    /**
     * Register the aliases from this module.
     */
    public function registerAliases(): void
    {
        $aliases = $this->get('aliases', []);

        foreach ($aliases as $alias => $class) {
            Facade::defaultAliases()->merge([$alias => $class]);
        }
    }

    /**
     * Register the service providers from this module.
     */
    public function registerProviders(): void
    {
        $providers = $this->get('providers', []);

        foreach ($providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Get the path to the cached *_module.php file.
     */
    public function getCachedServicesPath(): string
    {
        return $this->app->bootstrapPath('cache')
            .'/'.str_replace('\\', '_', strtolower($this->getName())).'_module.php';
    }
}
