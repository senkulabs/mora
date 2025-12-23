<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Database\Console\Seeds\SeederMakeCommand as LaravelSeederMakeCommand;
use Illuminate\Support\Str;
use SenkuLabs\Mora\Traits\MakeModular;

class SeederMakeCommand extends LaravelSeederMakeCommand
{
    use MakeModular;

    /**
     * Get the destination class path.
     */
    protected function getPath($name)
    {
        if ($module = $this->module()) {
            $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '');

            $modulePath = $this->laravel['modules']->getModulePath($module->getStudlyName());

            return $modulePath.'database/seeders/'.str_replace('\\', '/', $name).'.php';
        }

        return parent::getPath($name);
    }

    /**
     * Get the root namespace for the class.
     */
    protected function rootNamespace()
    {
        if ($module = $this->module()) {
            return $this->module_namespace($module->getStudlyName(), 'Database\\Seeders').'\\';
        }

        return parent::rootNamespace();
    }
}
