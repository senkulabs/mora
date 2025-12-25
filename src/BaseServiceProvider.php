<?php

namespace SenkuLabs\Mora;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Translation\Translator;
use SenkuLabs\Mora\Contracts\ActivatorInterface;
use SenkuLabs\Mora\Exceptions\InvalidActivatorClass;

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

        $this->registerModules();

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
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('modules.activator');
            $class = $app['config']->get('modules.activators.'.$activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'modules');

        $this->app->singleton(
            ModuleManifest::class,
            fn () => new ModuleManifest(
                new Filesystem,
                app(Contracts\RepositoryInterface::class)->getScanPaths(),
                $this->getCachedModulePath(),
                app(ActivatorInterface::class)
            )
        );
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
