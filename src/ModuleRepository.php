<?php

namespace SenkuLabs\Mora;

use Countable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use SenkuLabs\Mora\Exceptions\ModuleNotFoundException;

class ModuleRepository implements Countable
{
    /**
     * Application instance.
     */
    protected Container $app;

    /**
     * The module path.
     */
    protected ?string $path;

    /**
     * Config Repository
     */
    private ConfigRepository $config;

    /**
     * File system
     */
    private Filesystem $files;

    /**
     * Cached modules
     */
    private static array $modules = [];

    /**
     * The constructor.
     */
    public function __construct(Container $app, ?string $path = null)
    {
        $this->app = $app;
        $this->path = $path;
        $this->config = $app['config'];
        $this->files = $app['files'];
    }

    /**
     * Get & scan all modules.
     */
    public function scan(): array
    {
        if (! empty(self::$modules) && ! $this->app->runningUnitTests()) {
            return self::$modules;
        }

        $modulesPath = $this->getPath();
        $modules = [];

        $manifests = (array) $this->files->glob("{$modulesPath}/*/composer.json");

        foreach ($manifests as $manifest) {
            $json = json_decode($this->files->get($manifest), true);
            $composerName = $json['name'] ?? '';

            // Extract module name from composer name (e.g., "modules/guitar" -> "Guitar")
            $name = Str::studly(Str::afterLast($composerName, '/'));

            $modules[strtolower($name)] = new Module($this->app, $name, dirname($manifest));
        }

        self::$modules = $modules;

        return self::$modules;
    }

    /**
     * Get all modules.
     */
    public function all(): array
    {
        return $this->scan();
    }

    /**
     * Determine whether the given module exist.
     */
    public function has(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->all());
    }

    /**
     * Find a specific module.
     */
    public function find(string $name): ?Module
    {
        return $this->all()[strtolower($name)] ?? null;
    }

    /**
     * Find a specific module, if there return that, otherwise throw exception.
     *
     * @throws ModuleNotFoundException
     */
    public function findOrFail(string $name): Module
    {
        $module = $this->find($name);

        if ($module !== null) {
            return $module;
        }

        throw new ModuleNotFoundException("Module [{$name}] does not exist!");
    }

    /**
     * Get count from all modules.
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Get modules path.
     */
    public function getPath(): string
    {
        return $this->path ?: $this->config->get('modules.paths.modules', base_path('Modules'));
    }

    /**
     * Get module path for a specific module.
     */
    public function getModulePath(string $module): string
    {
        try {
            return $this->findOrFail($module)->getPath().'/';
        } catch (ModuleNotFoundException $e) {
            return $this->getPath().'/'.Str::studly($module).'/';
        }
    }

    /**
     * Get laravel filesystem instance.
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    /**
     * Reset cached modules.
     */
    public function resetModules(): static
    {
        self::$modules = [];

        return $this;
    }
}
