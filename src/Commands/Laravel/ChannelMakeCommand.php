<?php

namespace SenkuLabs\Mora\Commands\Laravel;

use Illuminate\Foundation\Console\ChannelMakeCommand as LaravelChannelMakeCommand;
use SenkuLabs\Mora\Traits\MakeModular;

class ChannelMakeCommand extends LaravelChannelMakeCommand
{
    use MakeModular;
}
