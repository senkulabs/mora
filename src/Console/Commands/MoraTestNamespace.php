<?php

namespace SenkuLabs\Mora\Console\Commands;

use Illuminate\Console\Command;
use SenkuLabs\Mora\Modular;
use Illuminate\Support\Str;

class MoraTestNamespace extends Command
{
    use Modular;

    protected $signature = 'mora:test-namespace';

    protected $description = 'Add module test namespace to root composer.json autoload-dev';

    public function handle(): int
    {
        $module = $this->module();

        if (! $module) {
            $this->components->error('Please specify a module using --module flag.');

            return self::FAILURE;
        }

        $composerPath = base_path('composer.json');

        if (! file_exists($composerPath)) {
            $this->components->error('composer.json not found in project root.');

            return self::FAILURE;
        }

        $composerContent = file_get_contents($composerPath);
        $composer = json_decode($composerContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->components->error('Failed to parse composer.json: ' . json_last_error_msg());

            return self::FAILURE;
        }

        // Get the module namespace from the module config
        $moduleNamespace = $module->namespace();
        $testNamespace = $moduleNamespace . 'Tests\\';

        // Build the relative path to the module's tests directory
        $moduleName = Str::of($module->name)->studly()->toString();
        $modulesDir = config('modules.namespace', 'Modules');
        $testPath = "{$modulesDir}/{$moduleName}/tests/";

        // Ensure autoload-dev exists
        if (! isset($composer['autoload-dev'])) {
            $composer['autoload-dev'] = [];
        }

        if (! isset($composer['autoload-dev']['psr-4'])) {
            $composer['autoload-dev']['psr-4'] = [];
        }

        // Check if namespace already exists
        if (isset($composer['autoload-dev']['psr-4'][$testNamespace])) {
            $this->components->warn("Namespace [{$testNamespace}] already exists in autoload-dev.");

            return self::SUCCESS;
        }

        // Add the namespace
        $composer['autoload-dev']['psr-4'][$testNamespace] = $testPath;

        // Write back to composer.json with pretty printing
        $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $newContent = json_encode($composer, $jsonOptions) . "\n";

        if (file_put_contents($composerPath, $newContent) === false) {
            $this->components->error('Failed to write to composer.json.');

            return self::FAILURE;
        }

        $this->components->info("Added [{$testNamespace}] => [{$testPath}] to autoload-dev.");
        $this->components->warn('Run "composer dump-autoload" to apply changes.');

        return self::SUCCESS;
    }
}
