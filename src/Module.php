<?php

namespace SenkuLabs\Mora;

use Illuminate\Container\Container;
use Illuminate\Support\Str;

class Module
{
    /**
     * The application instance.
     */
    protected Container $app;

    /**
     * The module name.
     */
    protected string $name;

    /**
     * The module path.
     */
    protected string $path;

    /**
     * The constructor.
     */
    public function __construct(Container $app, string $name, string $path)
    {
        $this->app = $app;
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get name in lower case.
     */
    public function getLowerName(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get name in studly case.
     */
    public function getStudlyName(): string
    {
        return Str::studly($this->name);
    }

    /**
     * Get name in kebab case.
     */
    public function getKebabName(): string
    {
        return Str::kebab($this->name);
    }

    /**
     * Get path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get extra path.
     */
    public function getExtraPath(?string $path = null): string
    {
        return $this->getPath().($path ? '/'.$path : '');
    }

    /**
     * Modules are always enabled when installed via composer.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Handle call __toString.
     */
    public function __toString(): string
    {
        return $this->getStudlyName();
    }
}
