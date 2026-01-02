<?php

namespace SenkuLabs\Mora\Tests\Unit;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use SenkuLabs\Mora\Module;

class ModuleTest extends TestCase
{
    private Container $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = new Container();
    }

    public function test_it_returns_the_module_name(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertEquals('TestModule', $module->getName());
    }

    public function test_it_returns_lowercase_name(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertEquals('testmodule', $module->getLowerName());
    }

    public function test_it_returns_studly_name(): void
    {
        $module = new Module($this->app, 'test-module', '/path/to/module');

        $this->assertEquals('TestModule', $module->getStudlyName());
    }

    public function test_it_returns_kebab_name(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertEquals('test-module', $module->getKebabName());
    }

    public function test_it_returns_the_module_path(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertEquals('/path/to/module', $module->getPath());
    }

    public function test_it_returns_extra_path_without_suffix(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertEquals('/path/to/module', $module->getExtraPath());
    }

    public function test_it_returns_extra_path_with_suffix(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertEquals('/path/to/module/app/Models', $module->getExtraPath('app/Models'));
    }

    public function test_module_is_always_enabled(): void
    {
        $module = new Module($this->app, 'TestModule', '/path/to/module');

        $this->assertTrue($module->isEnabled());
    }

    public function test_it_converts_to_string_as_studly_name(): void
    {
        $module = new Module($this->app, 'test-module', '/path/to/module');

        $this->assertEquals('TestModule', (string) $module);
    }
}
