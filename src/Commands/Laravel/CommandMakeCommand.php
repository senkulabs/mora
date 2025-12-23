<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ConsoleMakeCommand as LaravelConsoleMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class CommandMakeCommand extends LaravelConsoleMakeCommand
{
    use MakeModular;
}
