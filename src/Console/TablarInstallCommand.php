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
        if (version_compare(app()->version(), '11.0', '>=')) {

            $this->info('Running on Laravel 11.');

            $filePath = app_path('Http/Controllers/Controller.php');
            $newBaseClass = '\\Illuminate\\Routing\\Controller';

            $fileContents = file_get_contents($filePath);
            if (!str_contains($fileContents, 'extends ' . $newBaseClass)) {
                $fileContents = preg_replace(
                    '/class Controller\s*(extends \\\\?[a-zA-Z0-9_\\\\]+)?\s*\{/',
                    "class Controller extends $newBaseClass\n{",
                    $fileContents
                );
                file_put_contents($filePath, $fileContents);
                $this->info("The Controller class has been modified to extend $newBaseClass.");
            } else {
                $this->info("No changes made. The Controller class already extends $newBaseClass.");
            }
        }

    }
}
