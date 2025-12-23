<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as LaravelMigrateMakeCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use SenkuLabs\Mora\Module;
use SenkuLabs\Mora\Traits\PathNamespace;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputOption;

class MigrationMakeCommand extends LaravelMigrateMakeCommand
{
    use PathNamespace;

    /**
     * Create a new migration install command instance.
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }

    /**
     * Get the module instance for the current command.
     */
    protected function module(): ?Module
    {
        if ($name = $this->option('module')) {
            $modules = $this->getLaravel()->make('modules');

            if ($module = $modules->find($name)) {
                return $module;
            }

            throw new InvalidOptionException(sprintf('The "%s" module does not exist.', $name));
        }

        // Fallback to "used" module if set
        $modules = $this->getLaravel()->make('modules');
        if ($usedModule = $modules->getUsedNow()) {
            return $modules->find($usedModule);
        }

        return null;
    }

    /**
     * Configure the command to add the --module option.
     */
    protected function configure()
    {
        parent::configure();

        $this->getDefinition()->addOption(
            new InputOption(
                '--module',
                null,
                InputOption::VALUE_REQUIRED,
                'Create the migration inside the specified module'
            )
        );
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     */
    protected function getMigrationPath()
    {
        if ($module = $this->module()) {
            $modulePath = $this->laravel['modules']->getModulePath($module->getStudlyName());
            $migrationPath = config('modules.paths.generator.migration.path', 'database/migrations');
            $moduleDirectory = $modulePath.$migrationPath;

            $filesystem = $this->getLaravel()->make(Filesystem::class);
            if (! $filesystem->isDirectory($moduleDirectory)) {
                $filesystem->makeDirectory($moduleDirectory, 0755, true);
            }

            return $moduleDirectory;
        }

        return parent::getMigrationPath();
    }
}
