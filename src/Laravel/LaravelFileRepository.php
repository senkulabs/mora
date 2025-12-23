<?php

namespace SenkuLabs\Mora\Laravel;

use Illuminate\Container\Container;
use SenkuLabs\Mora\FileRepository;
use SenkuLabs\Mora\Module;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(Container $app, string $name, string $path): Module
    {
        return new LaravelModule($app, $name, $path);
    }
}
