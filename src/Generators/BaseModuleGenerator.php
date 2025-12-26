<?php

namespace SenkuLabs\Mora\Generators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use SenkuLabs\Mora\Contracts\ActivatorInterface;
use SenkuLabs\Mora\FileRepository;

class BaseModuleGenerator
{
    protected Filesystem $filesystem;
    protected FileRepository $module;
    protected ActivatorInterface $activator;
    protected string $name;
    protected string $moduleName;
    protected string $classNamePrefix;
    protected string $composerName;
    protected string $basePath;
    protected string $moduleNamespace;
    protected string $composerNamespace;

    public function __construct(
        string $name,
        Filesystem $filesystem,
        FileRepository $module,
        ActivatorInterface $activator
    ) {
        $this->name = $name;
        $this->filesystem = $filesystem;
        $this->module = $module;
        $this->activator = $activator;

        $this->moduleName = Str::kebab($name);
        $this->classNamePrefix = Str::studly($name);
        $this->moduleNamespace = config('modules.namespace', 'Modules');
        $this->composerNamespace = 'modules';
        $this->composerName = "{$this->composerNamespace}/{$this->moduleName}";
        $this->basePath = $this->module->getModulePath($this->classNamePrefix);
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getClassNamePrefix(): string
    {
        return $this->classNamePrefix;
    }

    public function getComposerName(): string
    {
        return $this->composerName;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function generate(): int
    {
        if ($this->module->has($this->classNamePrefix)) {
            return E_ERROR;
        }

        $this->generateFolders();
        $this->generateFiles();
        $this->registerInComposer();
        $this->activator->setActiveByName($this->classNamePrefix, true);

        return 0;
    }

    protected function registerInComposer(): void
    {
        $composerPath = base_path('composer.json');

        if (!$this->filesystem->exists($composerPath)) {
            return;
        }

        $composer = json_decode($this->filesystem->get($composerPath), true);

        // Add to require
        if (!isset($composer['require'][$this->composerName])) {
            $composer['require'][$this->composerName] = '@dev';
        }

        // Add to repositories
        if (!isset($composer['repositories'])) {
            $composer['repositories'] = [];
        }

        $repositoryExists = false;
        foreach ($composer['repositories'] as $repo) {
            if (isset($repo['url']) && $repo['url'] === "Modules/{$this->classNamePrefix}") {
                $repositoryExists = true;
                break;
            }
        }

        if (!$repositoryExists) {
            $composer['repositories'][$this->moduleName] = [
                'type' => 'path',
                'url' => "Modules/{$this->classNamePrefix}",
            ];
        }

        $this->filesystem->put(
            $composerPath,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );
    }

    protected function generateFolders(): void
    {
        $folders = [
            'app/Providers',
            'database/factories',
            'database/migrations',
            'database/seeders',
            'lang/en',
            'resources/js',
            'resources/css',
            'resources/views/components/layouts',
            'routes',
            'tests/Feature',
            'tests/Unit',
        ];

        foreach ($folders as $folder) {
            $path = $this->basePath . '/' . $folder;
            $this->filesystem->ensureDirectoryExists($path, 0755, true);
        }
    }

    protected function generateFiles(): void
    {
        $stubsPath = __DIR__.'/../../stubs';

        $files = [
            'composer.json' => 'composer.json',
            'module.json' => 'module.json',
            'package.json' => 'package.json',
            'gitignore' => '.gitignore',
            'lang/en/.gitkeep' => 'lang/en/.gitkeep',
            'routes/web.php' => 'routes/web.php',
            'views/index.blade.php' => 'resources/views/index.blade.php',
            'views/master.blade.php' => 'resources/views/components/layouts/master.blade.php',
            'assets/app.js' => 'resources/js/app.js',
            'assets/app.css' => 'resources/css/app.css',
            'provider.php' => "app/Providers/{$this->classNamePrefix}ServiceProvider.php",
        ];

        foreach ($files as $stub => $destination) {
            $stubFile = $stubsPath . '/' . $stub;

            if (!$this->filesystem->exists($stubFile)) {
                continue;
            }

            $contents = $this->filesystem->get($stubFile);
            $contents = $this->replacePlaceholders($contents);

            $destinationPath = $this->basePath . '/' . $destination;
            $this->filesystem->ensureDirectoryExists(dirname($destinationPath));
            $this->filesystem->put($destinationPath, $contents);
        }

        // Create .gitkeep files for empty directories
        $gitkeepDirs = [
            'database/factories',
            'database/migrations',
            'database/seeders',
            'tests/Feature',
            'tests/Unit',
        ];

        foreach ($gitkeepDirs as $dir) {
            $gitkeepPath = $this->basePath . '/' . $dir . '/.gitkeep';
            if (!$this->filesystem->exists($gitkeepPath)) {
                $this->filesystem->put($gitkeepPath, '');
            }
        }
    }

    protected function replacePlaceholders(string $contents): string
    {
        $placeholders = [
            'StubModuleNamespace' => $this->moduleNamespace,
            'StubComposerNamespace' => $this->composerNamespace,
            'StubModuleName' => $this->moduleName,
            'StubClassNamePrefix' => $this->classNamePrefix,
            'StubComposerName' => $this->composerName,
        ];

        $search = array_keys($placeholders);
        $replace = array_values($placeholders);

        return str_replace($search, $replace, $contents);
    }
}
