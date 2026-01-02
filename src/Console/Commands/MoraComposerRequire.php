<?php

namespace SenkuLabs\Mora\Console\Commands;

use Illuminate\Console\Command;
use SenkuLabs\Mora\Modular;
use Symfony\Component\Process\Process;

class MoraComposerRequire extends Command
{
    use Modular;

    protected $signature = 'mora:composer-require
                            {packages* : The composer packages to require}
                            {--dev : Require packages as dev dependencies}';

    protected $description = 'Require composer packages in a module';

    public function handle(): int
    {
        $module = $this->module();

        if (! $module) {
            $this->components->error('The --module option is required.');
            return self::FAILURE;
        }

        $packages = $this->argument('packages');
        $isDev = $this->option('dev');

        $modulePath = $module->path();

        // Check if composer.json exists
        if (! file_exists($modulePath . '/composer.json')) {
            $this->components->error("No composer.json found in module [{$module->name}].");
            return self::FAILURE;
        }

        $packageList = implode(' ', $packages);
        $devFlag = $isDev ? ' --dev' : '';

        $this->components->info("Requiring composer packages in module [{$module->name}]...");
        $this->components->twoColumnDetail('Packages', $packageList);
        $this->components->twoColumnDetail('Dev dependency', $isDev ? 'Yes' : 'No');

        $this->newLine();

        // Build the composer command
        $command = "composer require {$packageList}{$devFlag} --no-update";

        $process = Process::fromShellCommandline($command, $modulePath);
        $process->setTimeout(300);
        $process->setTty(Process::isTtySupported());

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->newLine();
            $this->components->error('Failed to require composer packages.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info('Composer packages required successfully.');
        $this->newLine();
        $this->components->warn('Run the following command to update dependencies:');
        $this->line('  composer update');

        return self::SUCCESS;
    }
}
