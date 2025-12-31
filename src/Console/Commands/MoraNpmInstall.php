<?php

namespace SenkuLabs\Mora\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MoraNpmInstall extends Command
{
    use Modularize;

    protected $signature = 'mora:npm-install
                            {packages* : The npm packages to install}
                            {--save-dev : Install packages as dev dependencies}';

    protected $description = 'Install npm packages in a module';

    public function handle(): int
    {
        $module = $this->module();

        if (! $module) {
            $this->components->error('The --module option is required.');
            return self::FAILURE;
        }

        $packages = $this->argument('packages');
        $isDev = $this->option('save-dev');

        $modulePath = $module->path();

        // Check if package.json exists
        if (! file_exists($modulePath . '/package.json')) {
            $this->components->error("No package.json found in module [{$module->name}].");
            return self::FAILURE;
        }

        $packageList = implode(' ', $packages);
        $devFlag = $isDev ? ' --save-dev' : '';

        $this->components->info("Installing npm packages in module [{$module->name}]...");
        $this->components->twoColumnDetail('Packages', $packageList);
        $this->components->twoColumnDetail('Dev dependency', $isDev ? 'Yes' : 'No');

        $this->newLine();

        // Build the npm command
        $command = "npm install {$packageList}{$devFlag} --package-lock-only";

        $process = Process::fromShellCommandline($command, $modulePath);
        $process->setTimeout(300);
        $process->setTty(Process::isTtySupported());

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->newLine();
            $this->components->error('Failed to install npm packages.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info('Npm packages installed successfully.');

        return self::SUCCESS;
    }
}
