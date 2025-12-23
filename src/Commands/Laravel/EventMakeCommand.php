<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\EventMakeCommand as LaravelEventMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class EventMakeCommand extends LaravelEventMakeCommand
{
    use MakeModular;
}
