<?php

namespace SenkuLabs\Mora\Tests\Unit;

use SenkuLabs\Mora\Exceptions\ModuleNotFoundException;
use SenkuLabs\Mora\Module;
use SenkuLabs\Mora\Tests\TestCase;

class ModuleRepositoryTest extends TestCase
{
    public function test_it_returns_empty_array_when_no_modules(): void
    {
        $repository = $this->app->make('modules');

        $this->assertEmpty($repository->all());
    }

    public function test_it_counts_zero_when_no_modules(): void
    {
        $repository = $this->app->make('modules');

        $this->assertEquals(0, $repository->count());
    }

    public function test_it_returns_false_when_module_does_not_exist(): void
    {
        $repository = $this->app->make('modules');

        $this->assertFalse($repository->has('NonExistent'));
    }

    public function test_it_returns_null_when_finding_non_existent_module(): void
    {
        $repository = $this->app->make('modules');

        $this->assertNull($repository->find('NonExistent'));
    }

    public function test_it_throws_exception_when_find_or_fail_non_existent_module(): void
    {
        $repository = $this->app->make('modules');

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('Module [NonExistent] does not exist!');

        $repository->findOrFail('NonExistent');
    }

    public function test_it_scans_and_finds_module(): void
    {
        $this->createTestModule('Blog');

        $repository = $this->app->make('modules');

        $this->assertTrue($repository->has('Blog'));
        $this->assertEquals(1, $repository->count());
    }

    public function test_it_finds_module_by_name(): void
    {
        $this->createTestModule('Blog');

        $repository = $this->app->make('modules');

        $module = $repository->find('Blog');

        $this->assertInstanceOf(Module::class, $module);
        $this->assertEquals('Blog', $module->getName());
    }

    public function test_it_finds_module_case_insensitively(): void
    {
        $this->createTestModule('Blog');

        $repository = $this->app->make('modules');

        $this->assertNotNull($repository->find('blog'));
        $this->assertNotNull($repository->find('BLOG'));
        $this->assertNotNull($repository->find('Blog'));
    }

    public function test_it_returns_correct_module_path(): void
    {
        $this->createTestModule('Blog');

        $repository = $this->app->make('modules');

        $expectedPath = $this->getModulesPath() . '/Blog/';

        $this->assertEquals($expectedPath, $repository->getModulePath('Blog'));
    }

    public function test_it_returns_inferred_path_for_non_existent_module(): void
    {
        $repository = $this->app->make('modules');

        $expectedPath = $this->getModulesPath() . '/NonExistent/';

        $this->assertEquals($expectedPath, $repository->getModulePath('NonExistent'));
    }

    public function test_it_scans_multiple_modules(): void
    {
        $this->createTestModule('Blog');
        $this->createTestModule('Shop');
        $this->createTestModule('Forum');

        $repository = $this->app->make('modules');

        $this->assertEquals(3, $repository->count());
        $this->assertTrue($repository->has('Blog'));
        $this->assertTrue($repository->has('Shop'));
        $this->assertTrue($repository->has('Forum'));
    }

    public function test_reset_modules_clears_cache(): void
    {
        $this->createTestModule('Blog');

        $repository = $this->app->make('modules');

        $this->assertEquals(1, $repository->count());

        // Reset and verify cache is cleared
        $repository->resetModules();

        // After reset, it should re-scan and still find the module
        $this->assertEquals(1, $repository->count());
    }
}
