<?php

namespace SenkuLabs\Mora\Tests\Feature\Commands;

use SenkuLabs\Mora\Tests\TestCase;

class MakeModuleCommandTest extends TestCase
{
    public function test_it_creates_a_new_module(): void
    {
        $this->artisan('make:module', ['name' => 'Blog'])
            ->assertSuccessful();

        $modulePath = $this->getModulesPath() . '/Blog';

        $this->assertDirectoryExists($modulePath);
    }

    public function test_it_creates_module_with_correct_folder_structure(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $modulePath = $this->getModulesPath() . '/Blog';

        // Check main directories
        $this->assertDirectoryExists($modulePath . '/app/Providers');
        $this->assertDirectoryExists($modulePath . '/database/factories');
        $this->assertDirectoryExists($modulePath . '/database/migrations');
        $this->assertDirectoryExists($modulePath . '/database/seeders');
        $this->assertDirectoryExists($modulePath . '/lang/en');
        $this->assertDirectoryExists($modulePath . '/resources/js');
        $this->assertDirectoryExists($modulePath . '/resources/css');
        $this->assertDirectoryExists($modulePath . '/resources/views');
        $this->assertDirectoryExists($modulePath . '/routes');
        $this->assertDirectoryExists($modulePath . '/tests/Feature');
        $this->assertDirectoryExists($modulePath . '/tests/Unit');
    }

    public function test_it_creates_composer_json_file(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $composerPath = $this->getModulesPath() . '/Blog/composer.json';

        $this->assertFileExists($composerPath);

        $composer = json_decode(file_get_contents($composerPath), true);

        $this->assertEquals('modules/blog', $composer['name']);
        $this->assertArrayHasKey('autoload', $composer);
        $this->assertArrayHasKey('psr-4', $composer['autoload']);
        $this->assertEquals('app/', $composer['autoload']['psr-4']['Modules\\Blog\\']);
    }

    public function test_it_creates_service_provider(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $providerPath = $this->getModulesPath() . '/Blog/app/Providers/BlogServiceProvider.php';

        $this->assertFileExists($providerPath);

        $content = file_get_contents($providerPath);

        $this->assertStringContainsString('namespace Modules\\Blog\\Providers', $content);
        $this->assertStringContainsString('class BlogServiceProvider', $content);
    }

    public function test_it_creates_routes_file(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $routesPath = $this->getModulesPath() . '/Blog/routes/web.php';

        $this->assertFileExists($routesPath);
    }

    public function test_it_creates_view_files(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $viewPath = $this->getModulesPath() . '/Blog/resources/views/index.blade.php';
        $layoutPath = $this->getModulesPath() . '/Blog/resources/views/components/layouts/master.blade.php';

        $this->assertFileExists($viewPath);
        $this->assertFileExists($layoutPath);
    }

    public function test_it_creates_asset_files(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $jsPath = $this->getModulesPath() . '/Blog/resources/js/app.js';
        $cssPath = $this->getModulesPath() . '/Blog/resources/css/app.css';

        $this->assertFileExists($jsPath);
        $this->assertFileExists($cssPath);
    }

    public function test_it_creates_language_files(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $langPath = $this->getModulesPath() . '/Blog/lang/en/messages.php';
        $langJsonPath = $this->getModulesPath() . '/Blog/lang/en.json';

        $this->assertFileExists($langPath);
        $this->assertFileExists($langJsonPath);
    }

    public function test_it_fails_when_module_already_exists(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $this->artisan('make:module', ['name' => 'Blog'])
            ->assertFailed();
    }

    public function test_it_creates_module_with_studly_case_name(): void
    {
        $this->artisan('make:module', ['name' => 'user-management']);

        $modulePath = $this->getModulesPath() . '/UserManagement';

        $this->assertDirectoryExists($modulePath);
    }

    public function test_it_creates_package_json_file(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $packagePath = $this->getModulesPath() . '/Blog/package.json';

        $this->assertFileExists($packagePath);
    }

    public function test_it_creates_gitignore_file(): void
    {
        $this->artisan('make:module', ['name' => 'Blog']);

        $gitignorePath = $this->getModulesPath() . '/Blog/.gitignore';

        $this->assertFileExists($gitignorePath);
    }
}
