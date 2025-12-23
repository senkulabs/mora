<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ProviderMakeCommand as LaravelProviderMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ProviderMakeCommand extends LaravelProviderMakeCommand
{
    use MakeModular;
}
