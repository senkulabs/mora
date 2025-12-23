<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\EnumMakeCommand as LaravelEnumMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class EnumMakeCommand extends LaravelEnumMakeCommand
{
    use MakeModular;
}
