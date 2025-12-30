<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SenkuLabs\Mora\Generators\BaseModuleGenerator;

class MakeModule extends Command
{
    protected $signature = 'make:module {name : The name of the module}';

    protected $description = 'Create a new module with simplified structure';

    public function handle(): int
    {
        $name = $this->argument('name');

        $this->components->info("Creating module: [{$name}]");

        $generator = new BaseModuleGenerator(
            $name,
            app(Filesystem::class),
            app('modules')
        );

        $code = $generator->generate();

        if ($code === E_ERROR) {
            $this->components->error("Module [{$name}] already exists!");
            return E_ERROR;
        }

        $classNamePrefix = $generator->getClassNamePrefix();
        $composerName = $generator->getComposerName();

        $this->newLine();

        // Show what was created
        $this->components->twoColumnDetail('Module path', base_path("Modules/{$classNamePrefix}"));
        $this->components->twoColumnDetail('Service Provider', "Modules/{$classNamePrefix}/app/Providers/{$classNamePrefix}ServiceProvider.php");

        $this->newLine();

        $this->components->info("Module [{$classNamePrefix}] created successfully.");

        $this->newLine();

        $this->components->warn("Run the following command to autoload the module service provider:");
        $this->line("  composer update {$composerName}");

        return 0;
    }
}
