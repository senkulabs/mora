<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Foundation\Console\ComponentMakeCommand;
use SenkuLabs\Mora\Modular;

class MakeComponent extends ComponentMakeCommand
{
	use Modular;

	protected function viewPath($path = '')
	{
		if ($module = $this->module()) {
			return $module->path("resources/views/{$path}");
		}

		return parent::viewPath($path);
	}
}
