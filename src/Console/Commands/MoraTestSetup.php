<?php

namespace SenkuLabs\Mora\Console\Commands;

use Illuminate\Console\Command;
use DOMDocument;
use DOMXPath;

class MoraTestSetup extends Command
{
    protected $signature = 'mora:test-setup';

    protected $description = 'Setup module tests in phpunit.xml and Pest.php';

    public function handle(): int
    {
        $this->setupPhpunit();
        $this->setupPest();

        return self::SUCCESS;
    }

    protected function setupPhpunit(): void
    {
        $phpunitPath = base_path('phpunit.xml');

        if (! file_exists($phpunitPath)) {
            $this->components->error('phpunit.xml not found in project root.');

            return;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        if (! $dom->load($phpunitPath)) {
            $this->components->error('Failed to parse phpunit.xml.');

            return;
        }

        $xpath = new DOMXPath($dom);
        $testsuites = $xpath->query('//testsuites/testsuite');

        if ($testsuites->length === 0) {
            $this->components->error('No testsuites found in phpunit.xml.');

            return;
        }

        $modulesPath = 'Modules/*/tests';
        $modified = false;

        foreach ($testsuites as $testsuite) {
            $name = $testsuite->getAttribute('name');
            $moduleDirectory = "{$modulesPath}/{$name}";

            // Check if module directory already exists in this testsuite
            $existingDirs = $xpath->query('directory', $testsuite);
            $alreadyExists = false;

            foreach ($existingDirs as $dir) {
                if (str_contains($dir->textContent, 'Modules/*/tests')) {
                    $alreadyExists = true;
                    break;
                }
            }

            if (! $alreadyExists) {
                $newDir = $dom->createElement('directory', $moduleDirectory);
                $testsuite->appendChild($newDir);
                $modified = true;
                $this->components->info("Added [{$moduleDirectory}] to [{$name}] testsuite.");
            } else {
                $this->components->warn("Module tests directory already exists in [{$name}] testsuite.");
            }
        }

        if ($modified) {
            $dom->save($phpunitPath);
            $this->components->info('phpunit.xml has been updated successfully.');
        }
    }

    protected function setupPest(): void
    {
        $pestPath = base_path('tests/Pest.php');

        if (! file_exists($pestPath)) {
            $this->components->warn('tests/Pest.php not found. Skipping Pest configuration.');

            return;
        }

        $content = file_get_contents($pestPath);
        $moduleFeaturePath = "../Modules/*/tests/Feature";

        // Check if module path already exists
        if (str_contains($content, $moduleFeaturePath)) {
            $this->components->warn('Module Feature tests path already exists in Pest.php.');

            return;
        }

        // Find the pest()->extend() chain and add the module path
        // Pattern: ->in('Feature') followed by optional semicolon or more chains
        $pattern = "/(->in\(['\"]Feature['\"]\))/";

        if (preg_match($pattern, $content)) {
            $replacement = "$1\n    ->in('{$moduleFeaturePath}')";
            $newContent = preg_replace($pattern, $replacement, $content, 1);

            if ($newContent !== $content) {
                file_put_contents($pestPath, $newContent);
                $this->components->info("Added [{$moduleFeaturePath}] to Pest.php.");
            }
        } else {
            $this->components->warn("Could not find ->in('Feature') in Pest.php. Please add module path manually.");
        }
    }
}
