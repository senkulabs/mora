<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ListenerMakeCommand as LaravelListenerMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ListenerMakeCommand extends LaravelListenerMakeCommand
{
    use MakeModular;
}
