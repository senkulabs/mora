<?php

namespace SenkuLabs\Mora;

use Illuminate\Container\Container;
use Illuminate\Support\Str;

class Module
{
    protected Container $app;

    protected string $name;

    protected string $path;

    public function __construct(Container $app, string $name, string $path)
    {
        $this->app = $app;
        $this->name = $name;
        $this->path = $path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLowerName(): string
    {
        return strtolower($this->name);
    }

    public function getStudlyName(): string
    {
        return Str::studly($this->name);
    }

    public function getKebabName(): string
    {
        return Str::kebab($this->name);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getExtraPath(?string $path = null): string
    {
        return $this->getPath().($path ? '/'.$path : '');
    }

    public function __toString(): string
    {
        return $this->getStudlyName();
    }
}
