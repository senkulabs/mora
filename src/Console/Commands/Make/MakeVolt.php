<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Volt\Console\MakeCommand;
use SenkuLabs\Mora\Modular;

class MakeVolt extends MakeCommand
{
    use Modular;

    public function handle()
    {
        if ($module = $this->module()) {
            // Check if component already exists
            if ($this->componentAlreadyExists($module) && ! $this->option('force')) {
                $this->components->error('Volt component already exists.');

                return false;
            }

            // Create the component
            $result = $this->createModuleComponent($module);

            if ($result === false) {
                return false;
            }

            // Register Volt in the module's service provider
            $this->registerVoltInServiceProvider($module);

            // Show success message with TAG
            $this->writeModuleWelcomeMessage($module, $result);

            return;
        }

        // No --module flag: use default behavior with correct path
        return parent::handle();
    }

    /**
     * Get the destination view path.
     *
     * Override parent to use the correct default path when no --module flag is specified,
     * ignoring any Volt::mount() paths set by module service providers.
     */
    protected function getPath($name): string
    {
        // If --module flag is provided, this method won't be called
        // (module components are handled in createModuleComponent)
        // So we only need to handle the default case here.

        // Use the default Livewire view path, not Volt::paths() which may be polluted by modules
        $mountPath = config('livewire.view_path', resource_path('views/livewire'));

        $argumentName = $this->argument('name');

        if (! str_contains($argumentName, '.blade.php')) {
            $view = str_replace('.', '/', $argumentName);
        } else {
            $view = $argumentName;
        }

        return $mountPath.'/'.Str::lower(Str::finish($view, '.blade.php'));
    }

    /**
     * Determine if the project is currently using class-based components.
     *
     * Override parent to check the correct default path.
     */
    protected function alreadyUsingClasses(): bool
    {
        $mountPath = config('livewire.view_path', resource_path('views/livewire'));

        if (! File::isDirectory($mountPath)) {
            return false;
        }

        $files = collect(File::allFiles($mountPath));

        foreach ($files as $file) {
            if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                $content = File::get($file->getPathname());

                if (str_contains($content, 'use Livewire\Volt\Component') ||
                    str_contains($content, 'new class extends Component')) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function componentAlreadyExists($module): bool
    {
        $name = $this->argument('name');

        $voltPath = $this->getModuleVoltPath($module, $name);

        if (File::exists($voltPath)) {
            return true;
        }

        // Check for class-based Livewire component with same name
        $classPath = $this->getModuleLivewireClassPath($module, $name);

        if (File::exists($classPath)) {
            return true;
        }

        return false;
    }

    protected function getModuleVoltPath($module, string $name): string
    {
        $view = $this->normalizeComponentName($name);

        return $module->path('resources/views/livewire/'.Str::lower(Str::finish($view, '.blade.php')));
    }

    protected function getModuleLivewireClassPath($module, string $name): string
    {
        $className = Str::of($name)
            ->split('/[.\/(\\\\)]+/')
            ->map([Str::class, 'studly'])
            ->join(DIRECTORY_SEPARATOR);

        return $module->path('app/Livewire/'.$className.'.php');
    }

    protected function normalizeComponentName(string $name): string
    {
        if (! str_contains($name, '.blade.php')) {
            return str_replace('.', '/', $name);
        }

        return $name;
    }

    protected function createModuleComponent($module): string|false
    {
        $name = $this->argument('name');
        $path = $this->getModuleVoltPath($module, $name);

        // Ensure directory exists
        $this->makeDirectory($path);

        // Get stub content
        $stub = $this->files->get($this->getStub());

        // Write the file
        $this->files->put($path, $stub);

        return $path;
    }

    protected function writeModuleWelcomeMessage($module, string $path): void
    {
        $moduleName = Str::kebab($module->name);
        $componentName = $this->getComponentName();

        $this->components->info(sprintf('%s [%s] created successfully.', $this->type, $path));
        $this->line('');
        // TAG format: <livewire:module::component-name />
        $this->line("<options=bold;fg=green>TAG:</> <livewire:{$moduleName}::{$componentName} />");
    }

    protected function getComponentName(): string
    {
        $name = $this->argument('name');

        return Str::of($name)
            ->replace('.blade.php', '')
            ->split('/[.\/(\\\\)]+/')
            ->map([Str::class, 'kebab'])
            ->implode('.');
    }

    protected function registerVoltInServiceProvider($module): void
    {
        $moduleName = Str::studly($module->name);
        $moduleKebab = Str::kebab($module->name);
        $serviceProviderPath = $module->path("app/Providers/{$moduleName}ServiceProvider.php");

        if (! File::exists($serviceProviderPath)) {
            $this->warn("Service provider not found: {$serviceProviderPath}");

            return;
        }

        $contents = File::get($serviceProviderPath);

        // Check if registerVoltComponents already exists
        if (Str::contains($contents, 'function registerVoltComponents')) {
            // Volt is already registered
            return;
        }

        // Add use statements
        $useStatements = [
            'use Livewire\\Livewire;',
            'use Livewire\\Volt\\Volt;',
            'use Livewire\\Volt\\ComponentFactory;',
            'use Illuminate\\Support\\Facades\\File;',
        ];

        foreach ($useStatements as $useStatement) {
            if (! Str::contains($contents, $useStatement)) {
                $contents = preg_replace(
                    '/(use Illuminate\\\\Support\\\\ServiceProvider;)/',
                    "$1\n{$useStatement}",
                    $contents
                );
            }
        }

        // Create the registerVoltComponents method
        $methodCode = <<<'PHP'

    protected function registerVoltComponents(): void
    {
        $voltPath = $this->app->basePath('Modules/StubModuleName/resources/views/livewire');

        Volt::mount([$voltPath]);

        // Register resolver for Volt components
        Livewire::resolveMissingComponent(function (string $name) use ($voltPath) {
            // Check if this is a Volt component for our module
            if (! str_starts_with($name, 'stub-module-kebab::')) {
                return null;
            }

            // Strip the module:: prefix to get component name
            $componentName = substr($name, strlen('stub-module-kebab::'));

            // Build the file path
            $filePath = $voltPath.'/'.str_replace('.', '/', $componentName).'.blade.php';

            if (! File::exists($filePath)) {
                return null;
            }

            $realPath = realpath($filePath);

            // Compile with the SIMPLE name so Volt can find it in mounted path
            $class = app(ComponentFactory::class)->make($componentName, $realPath);

            // Pre-register with simple name so getAlias() finds it FIRST
            Livewire::component($componentName, $class);

            return $class;
        });
    }
PHP;

        // Replace placeholders with actual module names
        $methodCode = str_replace('StubModuleName', $moduleName, $methodCode);
        $methodCode = str_replace('stub-module-kebab', $moduleKebab, $methodCode);

        // Add method before the closing class brace (with blank line before it)
        $contents = preg_replace(
            '/(\n\})(\s*)$/',
            "\n{$methodCode}\n}$2",
            $contents
        );

        // Add method call in boot() at the end, before the closing brace
        if (! Str::contains($contents, '$this->registerVoltComponents()')) {
            $contents = preg_replace(
                '/(public function boot\(\)[^{]*\{)(.*?)(\n    \})/s',
                "$1$2\n\n        \$this->registerVoltComponents();$3",
                $contents
            );
        }

        File::put($serviceProviderPath, $contents);

        $this->info("Registered Volt components in {$moduleName}ServiceProvider");
    }
}
