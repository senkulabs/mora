<?php

namespace SenkuLabs\Mora\Providers;

use Illuminate\Support\ServiceProvider;
use SenkuLabs\Mora\Contracts\RepositoryInterface;
use SenkuLabs\Mora\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
