<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\CastMakeCommand as LaravelCastMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class CastMakeCommand extends LaravelCastMakeCommand
{
    use MakeModular;
}
