<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\PolicyMakeCommand as LaravelPolicyMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class PolicyMakeCommand extends LaravelPolicyMakeCommand
{
    use MakeModular;
}
