<?php

namespace SenkuLabs\Mora\Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SenkuLabs\Mora\Support\ModuleConfig;

class ModuleConfigTest extends TestCase
{
    public function test_it_creates_module_config_with_properties(): void
    {
        $config = new ModuleConfig('TestModule', '/path/to/module');

        $this->assertEquals('TestModule', $config->name);
        $this->assertEquals('/path/to/module', $config->base_path);
        $this->assertInstanceOf(Collection::class, $config->namespaces);
        $this->assertTrue($config->namespaces->isEmpty());
    }

    public function test_it_creates_module_config_with_namespaces(): void
    {
        $namespaces = collect(['/path/to/module/app/' => 'Modules\\TestModule\\']);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $this->assertEquals('Modules\\TestModule\\', $config->namespaces->first());
    }

    public function test_path_returns_base_path_when_empty(): void
    {
        $config = new ModuleConfig('TestModule', '/path/to/module');

        $this->assertEquals('/path/to/module', $config->path());
    }

    public function test_path_returns_combined_path(): void
    {
        $config = new ModuleConfig('TestModule', '/path/to/module');

        $this->assertEquals('/path/to/module/app/Models', $config->path('app/Models'));
    }

    public function test_path_trims_trailing_slash(): void
    {
        $config = new ModuleConfig('TestModule', '/path/to/module');

        $this->assertEquals('/path/to/module/app', $config->path('app/'));
    }

    public function test_namespace_returns_first_namespace(): void
    {
        $namespaces = collect([
            '/path/to/module/app/' => 'Modules\\TestModule\\',
            '/path/to/module/src/' => 'Modules\\TestModule\\Src\\',
        ]);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $this->assertEquals('Modules\\TestModule\\', $config->namespace());
    }

    public function test_qualify_prepends_namespace_to_class(): void
    {
        $namespaces = collect(['/path/to/module/app/' => 'Modules\\TestModule\\']);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $this->assertEquals('Modules\\TestModule\\Models\\User', $config->qualify('Models\\User'));
    }

    public function test_qualify_handles_leading_backslash(): void
    {
        $namespaces = collect(['/path/to/module/app/' => 'Modules\\TestModule\\']);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $this->assertEquals('Modules\\TestModule\\Models\\User', $config->qualify('\\Models\\User'));
    }

    public function test_path_to_fully_qualified_class_name(): void
    {
        $namespaces = collect(['/path/to/module/app/' => 'Modules\\TestModule\\']);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $result = $config->pathToFullyQualifiedClassName('/path/to/module/app/Models/User.php');

        $this->assertEquals('Modules\\TestModule\\Models\\User', $result);
    }

    public function test_path_to_fully_qualified_class_name_throws_on_unknown_path(): void
    {
        $namespaces = collect(['/path/to/module/app/' => 'Modules\\TestModule\\']);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unable to infer qualified class name for '/unknown/path/Class.php'");

        $config->pathToFullyQualifiedClassName('/unknown/path/Class.php');
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $namespaces = collect(['/path/to/module/app/' => 'Modules\\TestModule\\']);

        $config = new ModuleConfig('TestModule', '/path/to/module', $namespaces);

        $expected = [
            'name' => 'TestModule',
            'base_path' => '/path/to/module',
            'namespaces' => ['/path/to/module/app/' => 'Modules\\TestModule\\'],
        ];

        $this->assertEquals($expected, $config->toArray());
    }
}
