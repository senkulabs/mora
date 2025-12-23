<?php

namespace SenkuLabs\Mora\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use SenkuLabs\Mora\Contracts\ActivatorInterface;
use SenkuLabs\Mora\Generators\BaseModuleGenerator;

class MakeModuleCommand extends Command
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
            app('modules'),
            app(ActivatorInterface::class)
        );

        $code = $generator->generate();

        if ($code === E_ERROR) {
            $this->components->error("Module [{$name}] already exists!");
            return E_ERROR;
        }

        $this->newLine();

        // Show what was created
        $this->components->twoColumnDetail('Module path', base_path("Modules/{$name}"));
        $this->components->twoColumnDetail('Service Provider', "Modules/{$name}/app/Providers/{$name}ServiceProvider.php");

        $this->newLine();

        // Run composer dump-autoload
        $this->components->task('Running composer dump-autoload', function () {
            Process::path(base_path())
                ->command('composer dump-autoload')
                ->run();
        });

        $this->newLine();
        $this->components->info("Module [{$name}] created successfully.");

        return 0;
    }
}
