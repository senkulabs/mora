<?php

namespace SenkuLabs\Mora;

use Illuminate\Support\Str;
use SenkuLabs\Mora\Support\ModuleConfig;
use SenkuLabs\Mora\Support\ModuleRegistry;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\select;

trait Modular
{
	protected function module(): ?ModuleConfig
	{
		$registry = $this->getLaravel()->make(ModuleRegistry::class);
		$name = $this->option('module');

		// If --module is provided with a value, validate and return the module
		if ($name) {
			if ($module = $registry->module($name)) {
				return $module;
			}

			throw new InvalidOptionException(sprintf('The "%s" module does not exist.', $name));
		}

		// If --module is not provided, prompt for module selection
		$modules = $registry->modules();

		if ($modules->isEmpty()) {
			return null;
		}

		$choices = $modules->keys()->prepend('None (use main app)')->all();

		$selected = select(
			label: 'Which module should this be created in?',
			options: $choices,
			default: 'None (use main app)',
		);

		if ($selected === 'None (use main app)') {
			return null;
		}

		return $registry->module($selected);
	}

	protected function configure()
	{
		parent::configure();

		$this->getDefinition()->addOption(
			new InputOption(
				'--module',
				null,
				InputOption::VALUE_OPTIONAL,
				'Create file inside a module'
			)
		);
	}

	protected function getDefaultNamespace($rootNamespace)
	{
		$namespace = parent::getDefaultNamespace($rootNamespace);
		$module = $this->module();

		if ($module && false === strpos($rootNamespace, $module->namespaces->first())) {
			$find = rtrim($rootNamespace, '\\');
			$replace = rtrim($module->namespaces->first(), '\\');
			$namespace = str_replace($find, $replace, $namespace);
		}

		return $namespace;
	}

	protected function qualifyClass($name)
	{
		$name = ltrim($name, '\\/');

		if ($module = $this->module()) {
			if (Str::startsWith($name, $module->namespaces->first())) {
				return $name;
			}
		}

		return parent::qualifyClass($name);
	}

	protected function qualifyModel(string $model)
	{
		if ($module = $this->module()) {
			$model = str_replace('/', '\\', ltrim($model, '\\/'));

			if (Str::startsWith($model, $module->namespace())) {
				return $model;
			}

			return $module->qualify('Models\\'.$model);
		}

		return parent::qualifyModel($model);
	}

	protected function getPath($name)
	{
		if ($module = $this->module()) {
			$name = Str::replaceFirst($module->namespaces->first(), '', $name);
		}

		$path = parent::getPath($name);

		if ($module) {
			// Set up our replacements as a [find -> replace] array
			$replacements = [
				$this->laravel->path() => $module->namespaces->keys()->first(),
				$this->laravel->basePath('tests/Tests') => $module->path('tests'),
				$this->laravel->databasePath() => $module->path('database'),
			];

			// Normalize all our paths for compatibility's sake
			$normalize = function($path) {
				return rtrim($path, '/').'/';
			};

			$find = array_map($normalize, array_keys($replacements));
			$replace = array_map($normalize, array_values($replacements));

			// And finally apply the replacements
			$path = str_replace($find, $replace, $path);
		}

		return $path;
	}

	public function call($command, array $arguments = [])
	{
		// Pass the --module flag on to subsequent commands
		if ($module = $this->option('module')) {
			$arguments['--module'] = $module;
		}

		return $this->runCommand($command, $arguments, $this->output);
	}
}
