<?php

namespace SenkuLabs\Mora\Traits;

use SenkuLabs\Mora\Module;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputOption;

trait Modular
{
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
                'Create the class inside the specified module'
            )
        );
    }
}
