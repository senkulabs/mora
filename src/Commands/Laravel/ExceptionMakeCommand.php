<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ExceptionMakeCommand as LaravelExceptionMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ExceptionMakeCommand extends LaravelExceptionMakeCommand
{
    use MakeModular;
}
