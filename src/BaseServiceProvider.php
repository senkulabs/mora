<?php

namespace SenkuLabs\Mora;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Translation\Translator;

class BaseServiceProvider extends ModulesServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerNamespaces();

        // Create @module() blade directive.
        Blade::if('module', function (string $name) {
            return module($name);
        });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();

        $this->registerMigrations();
        $this->registerTranslations();

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'modules');

        // Register the modularized commands service provider
        $this->app->register(Providers\ModularizedCommandsServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new Laravel\LaravelFileRepository($app, $path);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'modules');
    }

    protected function registerMigrations(): void
    {
        if (! $this->app['config']->get('modules.auto-discover.migrations', true)) {
            return;
        }

        $this->app->resolving(Migrator::class, function (Migrator $migrator) {
            $migration_path = $this->app['config']->get('modules.paths.generator.migration.path');
            collect(\SenkuLabs\Mora\Facades\Module::allEnabled())
                ->each(function (\SenkuLabs\Mora\Module $module) use ($migration_path, $migrator) {
                    $migrator->path($module->getExtraPath($migration_path));
                });
        });
    }

    protected function registerTranslations(): void
    {
        if (! $this->app['config']->get('modules.auto-discover.translations', true)) {
            return;
        }
        $this->callAfterResolving('translator', function (TranslatorContract $translator) {
            if (! $translator instanceof Translator) {
                return;
            }

            collect(\SenkuLabs\Mora\Facades\Module::allEnabled())
                ->each(function (\SenkuLabs\Mora\Module $module) use ($translator) {
                    $path = $module->getExtraPath($this->app['config']->get('modules.paths.generator.lang.path'));
                    $translator->addNamespace($module->getLowerName(), $path);
                    $translator->addJsonPath($path);
                });
        });
    }
}
