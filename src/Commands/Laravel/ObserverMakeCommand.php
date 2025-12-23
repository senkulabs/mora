<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ObserverMakeCommand as LaravelObserverMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ObserverMakeCommand extends LaravelObserverMakeCommand
{
    use MakeModular;
}
