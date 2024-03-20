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
        $this->comment('Tablar is now installed 🚀');
        $this->newLine();
        $this->comment('Run "npm install" first. Once the installation is done, run "php artisan tablar:export-auth"');
        $this->newLine();
        $this->line('Please Show your support ❤️ for Tablar by giving us a star on GitHub ⭐️');
        $this->info('https://github.com/takielias/tablar');
        $this->newLine(2);
    }
}
