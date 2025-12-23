<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\RequestMakeCommand as LaravelRequestMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class RequestMakeCommand extends LaravelRequestMakeCommand
{
    use MakeModular;
}
