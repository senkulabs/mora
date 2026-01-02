<?php

namespace SenkuLabs\Mora\Console\Commands\Make;

use Illuminate\Foundation\Console\ModelMakeCommand;
use SenkuLabs\Mora\Modular;

class MakeModel extends ModelMakeCommand
{
	use Modular;

	protected function getDefaultNamespace($rootNamespace)
	{
		if ($module = $this->module()) {
			$rootNamespace = rtrim($module->namespaces->first(), '\\');
		}

		return $rootNamespace.'\Models';
	}
}
