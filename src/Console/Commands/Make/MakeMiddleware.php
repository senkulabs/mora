<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Routing\Console\MiddlewareMakeCommand;
use SenkuLabs\Mora\Modular;

class MakeMiddleware extends MiddlewareMakeCommand
{
	use Modular;
}
