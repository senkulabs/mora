<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Foundation\Console\ViewMakeCommand;
use SenkuLabs\Mora\Console\Commands\Modularize;

class MakeView extends ViewMakeCommand
{
    use Modularize;

    /**
     * Get the view path for the module.
     */
    protected function viewPath($path = '')
    {
        if ($module = $this->module()) {
            $views = $module->path('resources/views');

            return $views.($path ? DIRECTORY_SEPARATOR.$path : $path);
        }

        return parent::viewPath($path);
    }
}