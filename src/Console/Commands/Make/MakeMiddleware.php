<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Routing\Console\MiddlewareMakeCommand;

class MakeMiddleware extends MiddlewareMakeCommand
{
	use Modularize;
}
