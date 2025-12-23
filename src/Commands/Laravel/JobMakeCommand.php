<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\JobMakeCommand as LaravelJobMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class JobMakeCommand extends LaravelJobMakeCommand
{
    use MakeModular;
}
