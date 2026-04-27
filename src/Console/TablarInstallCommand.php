<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarInstallCommand extends Command
{
    protected $signature = 'tablar:install';

    protected $description = 'Install Tablar scaffolding and export config';

    public function handle()
    {
        TablarPreset::install();
        TablarPreset::exportConfig();
        $this->info('Tablar scaffolding installed & config has been exported successfully.');
        $this->newLine();
        $this->checkController();
        $this->comment('Tablar is now installed 🚀');
        $this->newLine();
        $this->comment('Run "npm install" first. Once the installation is done, run "php artisan tablar:export-auth"');
        $this->newLine();
        $this->line('Please Show your support ❤️ for Tablar by giving us a star on GitHub ⭐️');
        $this->info('https://github.com/takielias/tablar');
        $this->newLine(2);
    }

    protected function checkController(): void
    {
        $major = (int) explode('.', app()->version())[0];

        if ($major < 11) {
            return;
        }

        $this->info("Running on Laravel {$major}.");

        $filePath = app_path('Http/Controllers/Controller.php');
        $fileContents = file_get_contents($filePath);

        $result = $this->patchControllerSource($fileContents);
        $newBaseClass = self::TARGET_BASE_CONTROLLER;

        if ($result === null) {
            $this->info("No changes made. The Controller class already extends {$newBaseClass}.");

            return;
        }

        file_put_contents($filePath, $result);
        $this->info("The Controller class has been modified to extend {$newBaseClass}.");
    }

    public const TARGET_BASE_CONTROLLER = '\\Illuminate\\Routing\\Controller';

    /**
     * Patch an `app/Http/Controllers/Controller.php` source so it extends
     * \Illuminate\Routing\Controller. Returns null when no change is needed
     * (already extending that base, or no `class Controller` declaration).
     *
     * Handles all post-L10 streamlined skeletons:
     *   - `class Controller {}`           (legacy)
     *   - `abstract class Controller {}`  (L11/L12/L13 default — `abstract` preserved)
     *   - `class Controller extends X {}` (already-extending other class — overwritten)
     */
    public function patchControllerSource(string $source): ?string
    {
        $target = self::TARGET_BASE_CONTROLLER;

        if (str_contains($source, 'extends '.$target)) {
            return null;
        }

        $patched = preg_replace_callback(
            '/(?P<prefix>(?:abstract\s+)?)class\s+Controller\s*(?:extends\s+\\\\?[a-zA-Z0-9_\\\\]+)?\s*\{/',
            fn (array $m): string => "{$m['prefix']}class Controller extends {$target}\n{",
            $source,
            1,
            $count
        );

        if ($patched === null || $count === 0) {
            return null;
        }

        return $patched;
    }
}
