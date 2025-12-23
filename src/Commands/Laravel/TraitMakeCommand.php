<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\TraitMakeCommand as LaravelTraitMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class TraitMakeCommand extends LaravelTraitMakeCommand
{
    use MakeModular;
}
