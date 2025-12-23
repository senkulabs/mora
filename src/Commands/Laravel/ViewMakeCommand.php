<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ViewMakeCommand as LaravelViewMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ViewMakeCommand extends LaravelViewMakeCommand
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
