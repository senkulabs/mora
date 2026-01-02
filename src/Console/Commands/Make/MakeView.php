<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Foundation\Console\ViewMakeCommand;
use SenkuLabs\Mora\Modular;

class MakeView extends ViewMakeCommand
{
    use Modular;

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