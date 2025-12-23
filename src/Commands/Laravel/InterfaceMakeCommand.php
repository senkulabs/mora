<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\InterfaceMakeCommand as LaravelInterfaceMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class InterfaceMakeCommand extends LaravelInterfaceMakeCommand
{
    use MakeModular;
}
