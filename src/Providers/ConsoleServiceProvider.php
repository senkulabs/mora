<?php

namespace SenkuLabs\Mora\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use SenkuLabs\Mora\Commands;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands(config('modules.commands', self::defaultCommands()->toArray()));
    }

    public function provides(): array
    {
        return self::defaultCommands()->toArray();
    }

    /**
     * Get the package default commands.
     */
    public static function defaultCommands(): Collection
    {
        return collect([
            // Other Commands
            Commands\ComposerUpdateCommand::class,
        ]);
    }
}