<?php

namespace SenkuLabs\Mora\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SenkuLabs\Mora\BaseServiceProvider;

abstract class TestCase extends Orchestra
{
    protected ?string $modulesPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modulesPath = $this->app->basePath('Modules');
        $this->cleanupModules();
    }

    protected function tearDown(): void
    {
        $this->cleanupModules();
        $this->modulesPath = null;

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BaseServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('modules.namespace', 'Modules');
        $app['config']->set('modules.paths.modules', $app->basePath('Modules'));
        $app['config']->set('modules.paths.app_folder', 'app/');
    }

    protected function getModulesPath(): string
    {
        return $this->modulesPath ?? $this->app->basePath('Modules');
    }

    protected function cleanupModules(): void
    {
        if ($this->modulesPath === null) {
            return;
        }

        if (is_dir($this->modulesPath)) {
            $this->deleteDirectory($this->modulesPath);
        }

        // Reset the cached modules in the repository
        if (isset($this->app) && $this->app->bound('modules')) {
            $this->app->make('modules')->resetModules();
        }
    }

    protected function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }

    protected function createTestModule(string $name = 'TestModule'): void
    {
        $this->artisan('make:module', ['name' => $name]);
        $this->app->make('modules')->resetModules();
    }
}
