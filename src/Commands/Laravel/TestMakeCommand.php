<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\TestMakeCommand as LaravelTestMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class TestMakeCommand extends LaravelTestMakeCommand
{
    use MakeModular;
}
