<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Illuminate\Support\ServiceProvider;

class StubClassNamePrefixServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('Modules/StubClassNamePrefix/resources/views'), 'StubModuleName');
    }
}
