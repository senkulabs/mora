<?php

namespace SenkuLabs\Mora\Console\Commands;

use Illuminate\Console\Command;

class MoraVite extends Command
{
    protected $signature = 'mora:vite';

    protected $description = 'Configure Vite and set "workspaces" in package.json for modular Laravel architecture';

    public function handle(): int
    {
        $this->components->info('Configuring Vite for modules...');
        $this->newLine();

        $packageJsonUpdated = $this->updatePackageJson();
        $viteConfigUpdated = $this->updateViteConfig();

        $this->newLine();

        if ($packageJsonUpdated || $viteConfigUpdated) {
            $this->components->info('Vite configured successfully.');

            if ($packageJsonUpdated) {
                $this->newLine();
                $this->components->warn('Please run [npm install] to activate workspace configuration.');
                $this->components->bulletList([
                    'Shared dependencies across workspaces will be hoisted to the root node_modules.',
                ]);
            }
        } else {
            $this->components->warn('No changes were made. Files may already be configured.');
        }

        return self::SUCCESS;
    }

    protected function updatePackageJson(): bool
    {
        $packageJsonPath = base_path('package.json');

        if (! file_exists($packageJsonPath)) {
            $this->components->error('package.json not found.');
            return false;
        }

        $content = file_get_contents($packageJsonPath);
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->components->error('Failed to parse package.json: ' . json_last_error_msg());
            return false;
        }

        if (isset($json['workspaces']) && $json['workspaces'] === ['Modules/*']) {
            $this->components->twoColumnDetail('package.json', '<fg=yellow>workspaces already configured</>');
            return false;
        }

        $json['workspaces'] = ['Modules/*'];

        $newContent = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

        file_put_contents($packageJsonPath, $newContent);

        $this->components->twoColumnDetail('package.json', '<fg=green>Added workspaces configuration</>');

        return true;
    }

    protected function updateViteConfig(): bool
    {
        $viteConfigPath = base_path('vite.config.js');

        if (! file_exists($viteConfigPath)) {
            $this->components->error('vite.config.js not found.');
            return false;
        }

        $content = file_get_contents($viteConfigPath);

        // Check if already configured
        if (str_contains($content, 'getModuleAssets')) {
            $this->components->twoColumnDetail('vite.config.js', '<fg=yellow>already configured for modules</>');
            return false;
        }

        $fsImport = "import { readdirSync, existsSync } from 'node:fs';";

        $getModuleAssetsFunction = <<<'JS'

function getModuleAssets() {
    const modulesDir = 'Modules';
    if (!existsSync(modulesDir)) return [];

    const assets = [];
    const modules = readdirSync(modulesDir, { withFileTypes: true })
        .filter(dirent => dirent.isDirectory())
        .map(dirent => dirent.name);

    for (const module of modules) {
        const cssPath = `${modulesDir}/${module}/resources/css/app.css`;
        const jsPath = `${modulesDir}/${module}/resources/js/app.js`;
        if (existsSync(cssPath)) assets.push(cssPath);
        if (existsSync(jsPath)) assets.push(jsPath);
    }

    return assets;
}

JS;

        $refreshArray = <<<'JS'
refresh: [
                'app/Livewire/**',
                'app/View/Components/**',
                'lang/**',
                'resources/lang/**',
                'resources/views/**',
                'routes/**',
                'Modules/*/app/Livewire/**',
                'Modules/*/app/View/Components/**',
                'Modules/*/lang/**',
                'Modules/*/resources/lang/**',
                'Modules/*/resources/views/**',
                'Modules/*/routes/**',
            ],
JS;

        // Check for refresh: true pattern
        $refreshPattern = '/refresh:\s*true\s*,?/';
        if (! preg_match($refreshPattern, $content)) {
            $this->components->error('Could not find "refresh: true" in vite.config.js.');
            return false;
        }

        // Add fs import at the top of the file
        $content = $fsImport . "\n" . $content;

        // Add getModuleAssets function before export default defineConfig
        $content = preg_replace(
            '/export\s+default\s+defineConfig/',
            $getModuleAssetsFunction . 'export default defineConfig',
            $content
        );

        // Update input to include getModuleAssets()
        $content = preg_replace(
            "/input:\s*\[\s*'resources\/css\/app\.css'\s*,\s*'resources\/js\/app\.js'\s*\]/",
            "input: ['resources/css/app.css', 'resources/js/app.js', ...getModuleAssets()]",
            $content
        );

        // Replace refresh: true with the array
        $content = preg_replace($refreshPattern, $refreshArray, $content);

        file_put_contents($viteConfigPath, $content);

        $this->components->twoColumnDetail('vite.config.js', '<fg=green>Updated for modular architecture</>');

        return true;
    }
}
