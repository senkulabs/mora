<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;
use SenkuLabs\Mora\Modular;

class MakeLivewire extends MakeCommand
{
    use Modular;

    public function getAliases(): array
    {
        return ['make:livewire', 'livewire:make'];
    }

    public function handle()
    {
        if ($module = $this->module()) {
            Config::set('livewire.class_namespace', $module->qualify('Livewire'));
            Config::set('livewire.view_path', $module->path('resources/views/livewire'));
        }

        parent::handle();

        // Register the component in the module's service provider
        if ($module = $this->module()) {
            $this->registerComponentInServiceProvider($module);
        }
    }

    public function writeWelcomeMessage()
    {
        // Output TAG before welcome message for modules
        if ($module = $this->module()) {
            $this->line("<options=bold;fg=green>TAG:</>   {$this->getComponentTag($module)}");
        }

        parent::writeWelcomeMessage();
    }

    protected function getComponentTag($module): string
    {
        $moduleName = Str::kebab($module->name);

        $componentPath = Str::of($this->argument('name'))
            ->split('/[.\/(\\\\)]+/')
            ->map([Str::class, 'kebab'])
            ->implode('.');

        return "<livewire:{$moduleName}::{$componentPath} />";
    }

    protected function createClass($force = false, $inline = false)
    {
        if ($module = $this->module()) {
            $name = Str::of($this->argument('name'))
                ->split('/[.\/(\\\\)]+/')
                ->map([Str::class, 'studly'])
                ->join(DIRECTORY_SEPARATOR);

            $classPath = $module->path('app/Livewire/'.$name.'.php');

            if (File::exists($classPath) && ! $force) {
                $this->line("<options=bold,reverse;fg=red> WHOOPS-IE-TOOTLES </> 😳 \n");
                $this->line("<fg=red;options=bold>Class already exists:</> {$classPath}");

                return false;
            }

            $this->ensureDirectoryExists($classPath);

            // Get the class contents and fix the view name for modules
            $contents = $this->parser->classContents($inline);
            $generatedViewName = $this->parser->viewName();
            $moduleViewName = $this->getModuleViewName($module);
            $contents = str_replace("'{$generatedViewName}'", "'{$moduleViewName}'", $contents);

            File::put($classPath, $contents);

            return $classPath;
        }

        return parent::createClass($force, $inline);
    }

    protected function getModuleViewName($module): string
    {
        $moduleName = Str::kebab($module->name);

        $componentPath = Str::of($this->argument('name'))
            ->split('/[.\/(\\\\)]+/')
            ->map([Str::class, 'kebab'])
            ->implode('.');

        return "{$moduleName}::livewire.{$componentPath}";
    }

    protected function registerComponentInServiceProvider($module): void
    {
        $moduleName = Str::studly($module->name);
        $serviceProviderPath = $module->path("app/Providers/{$moduleName}ServiceProvider.php");

        if (! File::exists($serviceProviderPath)) {
            $this->warn("Service provider not found: {$serviceProviderPath}");
            return;
        }

        $contents = File::get($serviceProviderPath);

        // Get component class name and full namespace
        $componentClassName = $this->parser->className();
        $componentNamespace = $this->parser->classNamespace();
        $componentAlias = $this->getModuleViewName($module);

        // Remove the "livewire." prefix for the component alias (e.g., "accordion::counter")
        $componentAlias = Str::of($componentAlias)->replace('livewire.', '');

        // Generate a unique import alias from the full path to avoid conflicts
        // e.g., User/Index -> UserIndex, Role/Index -> RoleIndex
        $importAlias = Str::of($this->argument('name'))
            ->split('/[.\/(\\\\)]+/')
            ->map([Str::class, 'studly'])
            ->implode('');

        $fullClassName = "{$componentNamespace}\\{$componentClassName}";
        $componentUseStatement = "use {$fullClassName} as {$importAlias};";
        $livewireUseStatement = "use Livewire\\Livewire;";
        $componentRegistration = "        Livewire::component('{$componentAlias}', {$importAlias}::class);";

        // Check if this exact component is already registered (by full class path)
        if (Str::contains($contents, $fullClassName)) {
            return;
        }

        // Add Livewire use statement if not present
        if (! Str::contains($contents, $livewireUseStatement)) {
            $contents = preg_replace(
                '/(use Illuminate\\\\Support\\\\ServiceProvider;)/',
                "$1\n{$livewireUseStatement}",
                $contents
            );
        }

        // Add component use statement
        $contents = preg_replace(
            '/(use Livewire\\\\Livewire;)/',
            "$1\n{$componentUseStatement}",
            $contents
        );

        // Check if registerLivewireComponents method exists
        if (Str::contains($contents, 'function registerLivewireComponents')) {
            // Add component registration to existing method
            $contents = preg_replace(
                '/(protected function registerLivewireComponents\(\).*?\{)/s',
                "$1\n{$componentRegistration}",
                $contents
            );
        } else {
            // Create the method and add call in boot()
            $methodCode = <<<PHP


    protected function registerLivewireComponents(): void
    {
{$componentRegistration}
    }
PHP;

            // Add method before the closing class brace
            $contents = preg_replace(
                '/(\n\})(\s*)$/',
                "{$methodCode}\n}$2",
                $contents
            );

            // Add method call in boot() at the end, before the closing brace
            if (! Str::contains($contents, '$this->registerLivewireComponents()')) {
                $contents = preg_replace(
                    '/(public function boot\(\)[^{]*\{)(.*?)(\n    \})/s',
                    "$1$2\n\n        \$this->registerLivewireComponents();$3",
                    $contents
                );
            }
        }

        File::put($serviceProviderPath, $contents);

        $this->info("Registered component in {$moduleName}ServiceProvider");
    }
}