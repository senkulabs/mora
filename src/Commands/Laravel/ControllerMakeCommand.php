<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Routing\Console\ControllerMakeCommand as LaravelControllerMakeCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SenkuLabs\Mora\Traits\MakeModular;

class ControllerMakeCommand extends LaravelControllerMakeCommand
{
    use MakeModular;

    /**
     * Parse the model for a controller.
     */
    protected function parseModel($model)
    {
        if (! $module = $this->module()) {
            return parent::parseModel($model);
        }

        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');
        $moduleNamespace = $this->module_namespace($module->getStudlyName());

        if (! Str::startsWith($model, $moduleNamespace)) {
            $model = $moduleNamespace.'Models\\'.$model;
        }

        return $model;
    }
}
