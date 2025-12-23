<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Routing\Console\MiddlewareMakeCommand as LaravelMiddlewareMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class MiddlewareMakeCommand extends LaravelMiddlewareMakeCommand
{
    use MakeModular;
}
