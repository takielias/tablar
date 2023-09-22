<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarUpdateCommand extends Command
{
    protected $signature = 'tablar:update';

    protected $description = 'Update Tablar scaffolding and export config';

    public function handle()
    {
        TablarPreset::update();
        $this->info('Tablar has been updated successfully.');
        $this->comment('Please run "npm install" first. Once the installation is done, run "npm run dev"');
    }
}
