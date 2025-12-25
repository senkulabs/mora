<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Illuminate\Support\ServiceProvider;

class StubClassNamePrefixServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'StubModuleName');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
