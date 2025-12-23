<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ModelMakeCommand as LaravelModelMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ModelMakeCommand extends LaravelModelMakeCommand
{
    use MakeModular;

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($module = $this->module()) {
            return $this->module_namespace($module->getStudlyName(), 'Models');
        }

        return parent::getDefaultNamespace($rootNamespace);
    }
}
