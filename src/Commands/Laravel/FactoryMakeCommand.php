<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as LaravelFactoryMakeCommand;
use Illuminate\Support\Str;
use SenkuLabs\Mora\Traits\MakeModular;

class FactoryMakeCommand extends LaravelFactoryMakeCommand
{
    use MakeModular;

    /**
     * Build the class with the given name.
     */
    protected function buildClass($name)
    {
        if (! $this->module()) {
            return parent::buildClass($name);
        }

        $module = $this->module();
        $factory = class_basename(Str::ucfirst(str_replace('Factory', '', $name)));

        $namespaceModel = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($factory);

        $model = class_basename($namespaceModel);

        $namespace = $this->module_namespace($module->getStudlyName(), 'Database\\Factories');

        $replace = [
            '{{ factoryNamespace }}' => $namespace,
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ factory }}' => $factory,
            '{{factory}}' => $factory,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get($this->getStub())
        );
    }

    /**
     * Get the destination class path.
     */
    protected function getPath($name)
    {
        if ($module = $this->module()) {
            $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '')->finish('Factory');

            $modulePath = $this->laravel['modules']->getModulePath($module->getStudlyName());

            return $modulePath.'database/factories/'.str_replace('\\', '/', $name).'.php';
        }

        return parent::getPath($name);
    }

    /**
     * Qualify the given model class base name.
     */
    protected function qualifyModel(string $model)
    {
        if ($module = $this->module()) {
            $model = ltrim($model, '\\/');
            $model = str_replace('/', '\\', $model);

            $moduleNamespace = $this->module_namespace($module->getStudlyName());

            // Don't double-qualify if already has module namespace
            if (str_starts_with($model, $moduleNamespace)) {
                return $model;
            }

            return $this->module_namespace($module->getStudlyName(), 'Models').'\\'.$model;
        }

        return parent::qualifyModel($model);
    }

    /**
     * Get the root namespace for the class.
     */
    protected function rootNamespace()
    {
        if ($module = $this->module()) {
            return $this->module_namespace($module->getStudlyName(), 'Database\\Factories').'\\';
        }

        return parent::rootNamespace();
    }
}
