<?php

namespace SenkuLabs\Mora\Tests\Feature\Commands;

use SenkuLabs\Mora\Tests\TestCase;

class MakeControllerCommandTest extends TestCase
{
    public function test_it_creates_controller_in_module(): void
    {
        $this->createTestModule('Blog');

        $this->artisan('make:controller', [
            'name' => 'PostController',
            '--module' => 'Blog',
        ])->assertSuccessful();

        $controllerPath = $this->getModulesPath() . '/Blog/app/Http/Controllers/PostController.php';

        $this->assertFileExists($controllerPath);
    }

    public function test_it_creates_controller_with_correct_namespace(): void
    {
        $this->createTestModule('Blog');

        $this->artisan('make:controller', [
            'name' => 'PostController',
            '--module' => 'Blog',
        ]);

        $controllerPath = $this->getModulesPath() . '/Blog/app/Http/Controllers/PostController.php';
        $content = file_get_contents($controllerPath);

        $this->assertStringContainsString('namespace Modules\\Blog\\Http\\Controllers', $content);
        $this->assertStringContainsString('class PostController', $content);
    }

    public function test_it_creates_resource_controller_in_module(): void
    {
        $this->createTestModule('Blog');

        $this->artisan('make:controller', [
            'name' => 'PostController',
            '--module' => 'Blog',
            '--resource' => true,
        ])->assertSuccessful();

        $controllerPath = $this->getModulesPath() . '/Blog/app/Http/Controllers/PostController.php';
        $content = file_get_contents($controllerPath);

        $this->assertStringContainsString('public function index()', $content);
        $this->assertStringContainsString('public function create()', $content);
        $this->assertStringContainsString('public function store(', $content);
        $this->assertStringContainsString('public function show(', $content);
        $this->assertStringContainsString('public function edit(', $content);
        $this->assertStringContainsString('public function update(', $content);
        $this->assertStringContainsString('public function destroy(', $content);
    }

    public function test_it_creates_invokable_controller_in_module(): void
    {
        $this->createTestModule('Blog');

        $this->artisan('make:controller', [
            'name' => 'ShowPostController',
            '--module' => 'Blog',
            '--invokable' => true,
        ])->assertSuccessful();

        $controllerPath = $this->getModulesPath() . '/Blog/app/Http/Controllers/ShowPostController.php';
        $content = file_get_contents($controllerPath);

        $this->assertStringContainsString('public function __invoke(', $content);
    }

    public function test_it_creates_controller_in_subdirectory(): void
    {
        $this->createTestModule('Blog');

        $this->artisan('make:controller', [
            'name' => 'Admin/PostController',
            '--module' => 'Blog',
        ])->assertSuccessful();

        $controllerPath = $this->getModulesPath() . '/Blog/app/Http/Controllers/Admin/PostController.php';

        $this->assertFileExists($controllerPath);

        $content = file_get_contents($controllerPath);

        $this->assertStringContainsString('namespace Modules\\Blog\\Http\\Controllers\\Admin', $content);
    }

    public function test_it_creates_controller_without_module_flag(): void
    {
        // Without --module flag, it should create in the app directory
        $this->artisan('make:controller', [
            'name' => 'TestController',
        ])->assertSuccessful();

        $controllerPath = $this->app->path('Http/Controllers/TestController.php');

        $this->assertFileExists($controllerPath);

        // Cleanup
        unlink($controllerPath);
    }
}
