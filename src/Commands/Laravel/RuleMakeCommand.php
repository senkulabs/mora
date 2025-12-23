<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\RuleMakeCommand as LaravelRuleMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class RuleMakeCommand extends LaravelRuleMakeCommand
{
    use MakeModular;
}
