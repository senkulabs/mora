<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Illuminate\Support\ServiceProvider;

class StubClassNamePrefixServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'StubModuleName');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'StubModuleName');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');

        $this->registerLivewireComponents();
    }

    protected function registerLivewireComponents()
    {
        // Register Livewire components here
    }
}
