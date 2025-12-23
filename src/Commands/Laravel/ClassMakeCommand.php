<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ClassMakeCommand as LaravelClassMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ClassMakeCommand extends LaravelClassMakeCommand
{
    use MakeModular;
}
