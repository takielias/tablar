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
        $this->comment('Please run "npm install" first. Once the installation is done, run "php artisan ui tablar:export-all"');
    }
}
