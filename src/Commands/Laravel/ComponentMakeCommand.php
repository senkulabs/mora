<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ComponentMakeCommand as LaravelComponentMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ComponentMakeCommand extends LaravelComponentMakeCommand
{
    use MakeModular;

    /**
     * Get the view path.
     */
    protected function viewPath($path = '')
    {
        if ($module = $this->module()) {
            $modulePath = $this->laravel['modules']->getModulePath($module->getStudlyName());
            $views = $modulePath.'resources/views/';

            return $views.($path ? DIRECTORY_SEPARATOR.$path : $path);
        }

        return parent::viewPath($path);
    }
}
