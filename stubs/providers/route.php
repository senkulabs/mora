<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'StubClassNamePrefix';

    public function boot(): void
    {
        Route::middleware('web')->group(module_path($this->name, '/routes/web.php'));
    }
}
