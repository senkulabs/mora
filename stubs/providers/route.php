<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'StubClassNamePrefix';

    public function boot(): void
    {
        Route::middleware('web')->group(base_path("Modules/{$this->name}/routes/web.php"));
    }
}
