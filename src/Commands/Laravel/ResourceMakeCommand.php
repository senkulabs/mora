<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ResourceMakeCommand as LaravelResourceMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ResourceMakeCommand extends LaravelResourceMakeCommand
{
    use MakeModular;
}
