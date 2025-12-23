<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ScopeMakeCommand as LaravelScopeMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ScopeMakeCommand extends LaravelScopeMakeCommand
{
    use MakeModular;
}
