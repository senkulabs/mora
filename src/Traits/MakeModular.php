<?php

namespace SenkuLabs\Mora\Traits;

use Illuminate\Support\Str;

trait MakeModular
{
    use Modular;
    use PathNamespace;

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $namespace = parent::getDefaultNamespace($rootNamespace);
        $module = $this->module();

        if ($module && false === strpos($rootNamespace, $this->module_namespace($module->getStudlyName()))) {
            $find = rtrim($rootNamespace, '\\');
            $replace = rtrim($this->module_namespace($module->getStudlyName()), '\\');
            $namespace = str_replace($find, $replace, $namespace);
        }

        return $namespace;
    }

    /**
     * Qualify the given model class base name.
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        if ($module = $this->module()) {
            $moduleNamespace = $this->module_namespace($module->getStudlyName());
            if (Str::startsWith($name, $moduleNamespace)) {
                return $name;
            }
        }

        return parent::qualifyClass($name);
    }

    /**
     * Qualify the given model class base name.
     */
    protected function qualifyModel(string $model)
    {
        if ($module = $this->module()) {
            $model = str_replace('/', '\\', ltrim($model, '\\/'));
            $moduleNamespace = $this->module_namespace($module->getStudlyName());

            if (Str::startsWith($model, $moduleNamespace)) {
                return $model;
            }

            return $this->module_namespace($module->getStudlyName(), 'Models').'\\'.$model;
        }

        return parent::qualifyModel($model);
    }

    /**
     * Get the destination class path.
     */
    protected function getPath($name)
    {
        if ($module = $this->module()) {
            $moduleNamespace = $this->module_namespace($module->getStudlyName());
            $name = Str::replaceFirst($moduleNamespace, '', $name);
        }

        $path = parent::getPath($name);

        if ($module = $this->module()) {
            $modulePath = $this->laravel['modules']->getModulePath($module->getStudlyName());
            $appPath = $this->app_path();

            // Set up our replacements as a [find -> replace] array
            $replacements = [
                $this->laravel->path() => $modulePath.$appPath,
                $this->laravel->basePath('tests/Tests') => $modulePath.'tests',
                $this->laravel->basePath('tests') => $modulePath.'tests',
                $this->laravel->databasePath() => $modulePath.'database',
            ];

            // Normalize all our paths for compatibility's sake
            $normalize = fn ($path) => rtrim($path, '/').'/';

            $find = array_map($normalize, array_keys($replacements));
            $replace = array_map($normalize, array_values($replacements));

            // And finally apply the replacements
            $path = str_replace($find, $replace, $path);
        }

        return $path;
    }

    /**
     * Call another console command, passing the --module flag.
     */
    public function call($command, array $arguments = [])
    {
        // Pass the --module flag on to subsequent commands
        if ($module = $this->option('module')) {
            $arguments['--module'] = $module;
        }

        return $this->runCommand($command, $arguments, $this->output);
    }
}
